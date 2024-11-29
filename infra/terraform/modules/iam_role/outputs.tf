# Task Role Outputs
output "task_role_name" {
  description = "The name of the Task Role"
  value       = aws_iam_role.task_role.name
}

output "task_role_arn" {
  description = "The ARN of the Task Role"
  value       = aws_iam_role.task_role.arn
}

output "task_policy_name" {
  description = "The name of the Task IAM policy"
  value       = aws_iam_policy.task_policy.name
}

output "task_policy_arn" {
  description = "The ARN of the Task IAM policy"
  value       = aws_iam_policy.task_policy.arn
}

# Task Execution Role Outputs
output "execution_role_name" {
  description = "The name of the Task Execution Role"
  value       = aws_iam_role.execution_role.name
}

output "execution_role_arn" {
  description = "The ARN of the Task Execution Role"
  value       = aws_iam_role.execution_role.arn
}
