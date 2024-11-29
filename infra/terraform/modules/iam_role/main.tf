# Task Role
resource "aws_iam_role" "task_role" {
  name               = var.task_role_name
  assume_role_policy = jsonencode(var.task_assume_role_policy)

  tags = var.tags
}

resource "aws_iam_policy" "task_policy" {
  name        = var.task_policy_name
  description = var.task_policy_description

  policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Effect   = "Allow",
        Action   = [
          "dynamodb:*"
        ],
        Resource = [
          var.dynamodb_table_arn,
          "${var.dynamodb_table_arn}/index/*"
        ]
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "task_policy_attachment" {
  role       = aws_iam_role.task_role.name
  policy_arn = aws_iam_policy.task_policy.arn
}

# Task Execution Role
resource "aws_iam_role" "execution_role" {
  name               = var.execution_role_name
  assume_role_policy = jsonencode(var.execution_assume_role_policy)

  tags = var.tags
}

resource "aws_iam_role_policy_attachment" "ecs_execution_policy_attachment" {
  role       = aws_iam_role.execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_role_policy_attachment" "cloudwatch_logs_policy_attachment" {
  role       = aws_iam_role.execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/CloudWatchLogsFullAccess"
}
