provider "aws" {
  region = "us-east-1"
}

module "dynamodb" {
  source = "./modules/dynamodb"

  name                 = "Tasks"
  hash_key             = "taskId"
  read_capacity  = 1
  write_capacity = 1
  tags = {
    Environment = "dev"
    Project     = "Technical-Challenge"
  }
}
