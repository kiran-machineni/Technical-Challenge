# Task Role Variables
variable "task_role_name" {
  description = "The name of the Task IAM role"
  type        = string
}

variable "task_policy_name" {
  description = "The name of the Task IAM policy"
  type        = string
}

variable "task_policy_description" {
  description = "A description for the Task IAM policy"
  type        = string
}

variable "task_assume_role_policy" {
  description = "The policy that specifies who can assume the Task Role"
  type        = any
}

variable "dynamodb_table_arn" {
  description = "The ARN of the DynamoDB table"
  type        = string
}

# Task Execution Role Variables
variable "execution_role_name" {
  description = "The name of the Task Execution Role"
  type        = string
}

variable "execution_assume_role_policy" {
  description = "The policy that specifies who can assume the Task Execution Role"
  type        = any
}

variable "tags" {
  description = "Tags to apply to IAM roles"
  type        = map(string)
  default     = {}
}
