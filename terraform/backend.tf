terraform {
  backend "gcs" {
    bucket = "fiap-pos-graduacao-terraform-state"
    prefix = "hackathon-worker"
  }
}
