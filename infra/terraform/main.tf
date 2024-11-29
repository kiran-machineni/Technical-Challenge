provider "aws" {
  region = "us-east-1"
}

module "dynamodb" {
  source = "./modules/dynamodb"

  name          = "Tasks"
  hash_key      = "taskId"
  read_capacity = 1
  write_capacity = 1

  tags = {
    Environment = "dev"
    Project     = "Technical-Challenge"
  }
}

module "iam_role" {
  source = "./modules/iam_role"

  # Task Role Configuration
  task_role_name          = "Tasks-DynamoDB-Access-Role"
  task_policy_name        = "Tasks-DynamoDB-Full-Access"
  task_policy_description = "Policy granting full access to the Tasks DynamoDB table"
  task_assume_role_policy = {
    Version = "2012-10-17",
    Statement = [
      {
        Effect = "Allow",
        Principal = {
          Service = ["ecs-tasks.amazonaws.com"]
        },
        Action = "sts:AssumeRole"
      }
    ]
  }
  dynamodb_table_arn = module.dynamodb.table_arn

  # Task Execution Role Configuration
  execution_role_name          = "task-management-app-execution-role"
  execution_assume_role_policy = {
    Version = "2012-10-17",
    Statement = [
      {
        Effect = "Allow",
        Principal = {
          Service = ["ecs-tasks.amazonaws.com"]
        },
        Action = "sts:AssumeRole"
      }
    ]
  }

  tags = {
    Environment = "dev"
    Project     = "Technical-Challenge"
  }
}
