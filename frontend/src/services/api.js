// src/services/api.js

import axios from "axios"

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
})

export const getTasks = () => api.get("/tasks")
export const getTask = (taskId) => api.get(`/tasks/${taskId}`)
export const createTask = (task) => api.post("/tasks", task)
export const updateTask = (taskId, task) => api.put(`/tasks/${taskId}`, task)
export const deleteTask = (taskId) => api.delete(`/tasks/${taskId}`)

export default api
