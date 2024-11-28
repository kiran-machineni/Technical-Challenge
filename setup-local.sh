#!/bin/bash

# Ensure the script stops if any command fails
set -e

# Prerequisites:
# 0. Install Docker, Docker compose
# 1. Install AWS CLI: https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html
# 2. Authenticate to AWS CLI by running: aws configure
# 3. Provision DynamoDB by running the terraform workflow or manually create it from AWS CLI
# Example:
#    aws dynamodb create-table \
#      --table-name Tasks \
#      --attribute-definitions AttributeName=TaskId,AttributeType=S \
#      --key-schema AttributeName=TaskId,KeyType=HASH \
#      --provisioned-throughput ReadCapacityUnits=5,WriteCapacityUnits=5 \


echo "Building the backend Docker image..."
docker build -t taskmanager-backend:latest ./backend

echo "Building the frontend Docker image..."
docker build -t taskmanager-frontend:latest ./frontend

# Export AWS credentials into the environment for Docker Compose
export AWS_ACCESS_KEY_ID=$(aws configure get aws_access_key_id)
export AWS_SECRET_ACCESS_KEY=$(aws configure get aws_secret_access_key)
export AWS_REGION=$(aws configure get region)

echo "Starting the Task Manager application using Docker Compose..."
docker-compose up -d

echo "Task Manager backend and frontend services are up and running!"
echo "Access the backend at http://localhost:8080"
echo "Access the frontend at http://localhost:3000"
