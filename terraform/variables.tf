variable "app_version" {
  description = "The version of the application to deploy"
  type        = string
}

variable "shared_state_encryption_key" {
  description = "The encryption key to use for the shared state"
  type        = string
}

variable "mail_username" {
  description = "The username to use to send e-mails"
  type        = string
}

variable "mail_password" {
  description = "The password to use to send e-mails"
  type        = string
}