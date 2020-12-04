/*
 Terraform docs re: configuring back end: https://www.terraform.io/docs/backends/types/gcs.html
 Use a bucket to store state so we can run it everywhere
*/
terraform {
  backend "gcs" {
    prefix = "terraform/facebook-storybook"
    bucket = "psessentials-production"
  }
}
