output "dynamodb_table_name" {
  description = "The name of the DynamoDB table"
  value       = module.dynamodb.table_name
}

output "dynamodb_table_arn" {
  description = "The ARN of the DynamoDB table"
  value       = module.dynamodb.table_arn
}

# Task Role Outputs
output "task_role_name" {
  description = "The name of the Task Role"
  value       = module.iam_role.task_role_name
}

output "task_role_arn" {
  description = "The ARN of the Task Role"
  value       = module.iam_role.task_role_arn
}

# Task Execution Role Outputs
output "task_execution_role_name" {
  description = "The name of the Task Execution Role"
  value       = module.iam_role.execution_role_name
}

output "task_execution_role_arn" {
  description = "The ARN of the Task Execution Role"
  value       = module.iam_role.execution_role_arn
}
