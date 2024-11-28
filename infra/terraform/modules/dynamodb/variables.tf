variable "name" {
  description = "The name of the DynamoDB table"
  type        = string
}

variable "hash_key" {
  description = "The primary key of the DynamoDB table"
  type        = string
  default     = "taskId"
}

variable "read_capacity" {
  description = "The number of read capacity units for the table"
  type        = number
  default     = 1
}

variable "write_capacity" {
  description = "The number of write capacity units for the table"
  type        = number
  default     = 1
}

variable "tags" {
  description = "Tags to apply to the DynamoDB table"
  type        = map(string)
  default     = {}
}
