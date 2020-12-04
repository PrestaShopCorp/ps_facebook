# https://www.terraform.io/docs/providers/google/index.html
provider "google" {
  version = "3.19.0"
  //"2.7.0"
  project = local.project
  region  = data.terraform_remote_state.services.outputs.region
  zone    = data.terraform_remote_state.services.outputs.default_zone
}

# https://www.terraform.io/docs/providers/kubernetes/index.html
provider "kubernetes" {
  version = "1.10.0"
  //">= 1.7.0"
  host = "https://${data.terraform_remote_state.services.outputs.kubernetes_host}"
  client_certificate = base64decode(
  data.terraform_remote_state.services.outputs.kubernetes_client_certificate,
  )
  client_key = base64decode(
  data.terraform_remote_state.services.outputs.kubernetes_client_key,
  )
  cluster_ca_certificate = base64decode(
  data.terraform_remote_state.services.outputs.kubernetes_cluster_ca_certificate,
  )
}

