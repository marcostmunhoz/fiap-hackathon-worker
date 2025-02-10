data "terraform_remote_state" "shared_state" {
  backend = "gcs"

  config = {
    bucket         = "fiap-pos-graduacao-terraform-state"
    prefix         = "hackathon-monorepo"
    encryption_key = var.shared_state_encryption_key
  }
}

data "google_service_account" "service_account" {
  account_id = data.terraform_remote_state.shared_state.outputs.service_account_id
}

resource "google_cloud_run_v2_service" "worker_service" {
  name                = "hackathon-worker-service"
  location            = local.google.region
  deletion_protection = false
  ingress             = "INGRESS_TRAFFIC_ALL"

  template {
    scaling {
      max_instance_count = 1
      min_instance_count = 1
    }

    volumes {
      name = "GOOGLE_SERVICE_ACCOUNT_KEY_FILE"

      secret {
        secret = "HACKATHON_APPLICATION_SERVICE_ACCOUNT_KEY"
        items {
          version = 1
          path    = "GOOGLE_SERVICE_ACCOUNT_KEY_FILE.json"
        }
      }
    }

    service_account = data.google_service_account.service_account.email

    vpc_access {
      network_interfaces {
        network    = data.terraform_remote_state.shared_state.outputs.network
        subnetwork = data.terraform_remote_state.shared_state.outputs.subnetwork
      }
    }

    containers {
      image = "docker.io/marcostmunhoz/fiap-hackathon-worker:${var.app_version}"

      env {
        name  = "APP_VERSION"
        value = var.app_version
      }

      env {
        name = "APP_KEY"
        value_source {
          secret_key_ref {
            secret = "HACKATHON_WORKER_APP_KEY"
            version = "1"
          }
        }
      }

      env {
        name = "DB_HOST"
        value_source {
          secret_key_ref {
            secret  = "HACKATHON_DB_HOST"
            version = "1"
          }
        }
      }

      env {
        name = "DB_PASSWORD"
        value_source {
          secret_key_ref {
            secret  = "HACKATHON_WORKER_DB_PASSWORD"
            version = "1"
          }
        }
      }

      env {
        name = "MAIL_USERNAME"
        value_source {
          secret_key_ref {
            secret  = "HACKATHON_WORKER_MAIL_USERNAME"
            version = 1
          }
        }
      }

      env {
        name = "MAIL_PASSWORD"
        value_source {
          secret_key_ref {
            secret  = "HACKATHON_WORKER_MAIL_PASSWORD"
            version = 1
          }
        }
      }

      volume_mounts {
        name       = "GOOGLE_SERVICE_ACCOUNT_KEY_FILE"
        mount_path = "/secrets"
      }
    }
  }

  traffic {
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
    percent = 100
  }
}

resource "google_cloud_run_service_iam_binding" "default" {
  location = local.google.region
  service  = google_cloud_run_v2_service.worker_service.name
  role     = "roles/run.invoker"
  members = [
    "allUsers"
  ]
}

resource "google_cloud_scheduler_job" "worker_heartbeat_scheduler" {
  name     = "hackathon-worker-service-heartbeat"
  schedule = "* * * * *"

  http_target {
    uri         = "${google_cloud_run_v2_service.worker_service.uri}/health"
    http_method = "GET"
  }
}