resource "google_secret_manager_secret" "worker_mail_username" {
  secret_id = "HACKATHON_WORKER_MAIL_USERNAME"
  project   = local.google.project
  replication {
    auto {}
  }
}

resource "google_secret_manager_secret_version" "worker_mail_username_version" {
  secret      = google_secret_manager_secret.worker_mail_username.id
  secret_data = var.mail_username
}

resource "google_secret_manager_secret" "worker_mail_password" {
  secret_id = "HACKATHON_WORKER_MAIL_PASSWORD"
  project   = local.google.project
  replication {
    auto {}
  }
}

resource "google_secret_manager_secret_version" "worker_mail_password_version" {
  secret      = google_secret_manager_secret.worker_mail_password.id
  secret_data = var.mail_password
}