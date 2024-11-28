// src/pages/TaskList.jsx

import { useEffect, useState } from "react"
import { getTasks, deleteTask } from "../services/api"
import { Button, Table, Container, Row, Col, Alert } from "react-bootstrap"
import TaskCreateModal from "../components/TaskCreateModal"
import TaskEditModal from "../components/TaskEditModal"

const TaskList = () => {
  const [tasks, setTasks] = useState([])
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [showEditModal, setShowEditModal] = useState(false)
  const [currentTask, setCurrentTask] = useState(null)
  const [errorMessage, setErrorMessage] = useState("")
  const [successMessage, setSuccessMessage] = useState("")

  useEffect(() => {
    fetchTasks()
  }, [])

  const fetchTasks = async () => {
    try {
      const response = await getTasks()
      setTasks(response.data.data)
    } catch (error) {
      console.error("Error fetching tasks:", error)
      setErrorMessage("Failed to fetch tasks. Please try again later.")
    }
  }

  const handleDelete = async (taskId) => {
    if (window.confirm("Are you sure you want to delete this task?")) {
      try {
        await deleteTask(taskId)
        setTasks(tasks.filter(task => task.taskId !== taskId))
        setSuccessMessage("Task deleted successfully!")
        // Clear success message after 3 seconds
        setTimeout(() => setSuccessMessage(""), 3000)
      } catch (error) {
        console.error("Error deleting task:", error)
        setErrorMessage("Failed to delete task. Please try again.")
        // Clear error message after 3 seconds
        setTimeout(() => setErrorMessage(""), 3000)
      }
    }
  }

  const handleEdit = (task) => {
    setCurrentTask(task)
    setShowEditModal(true)
  }

  return (
    <Container>
      {/* Success Alert */}
      {successMessage && (
        <Alert variant="success" onClose={() => setSuccessMessage("")} dismissible>
          {successMessage}
        </Alert>
      )}

      {/* Error Alert */}
      {errorMessage && (
        <Alert variant="danger" onClose={() => setErrorMessage("")} dismissible>
          {errorMessage}
        </Alert>
      )}

      <Row className="mb-4">
        <Col>
          <h2 className="text-center">Tasks</h2>
        </Col>
      </Row>

      <Row className="mb-3">
        <Col className="d-flex justify-content-end">
          <Button variant="primary" onClick={() => setShowCreateModal(true)}>
            Create Task
          </Button>
        </Col>
      </Row>

      <Row>
        <Col>
          {tasks.length === 0 ? (
            <p className="text-center">No tasks available.</p>
          ) : (
            <div className="table-responsive">
              <Table striped bordered hover className="text-center">
                <thead className="table-dark">
                  <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {tasks.map(task => (
                    <tr key={task.taskId}>
                      <td>{task.title}</td>
                      <td>{task.description}</td>
                      <td>{task.task_status}</td>
                      <td>{new Date(task.dueDate).toLocaleDateString()}</td>
                      <td>
                        <Button
                          variant="info"
                          size="sm"
                          className="me-2"
                          onClick={() => handleEdit(task)}
                        >
                          Edit
                        </Button>
                        <Button
                          variant="danger"
                          size="sm"
                          onClick={() => handleDelete(task.taskId)}
                        >
                          Delete
                        </Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </Table>
            </div>
          )}
        </Col>
      </Row>

      {/* Create Task Modal */}
      <TaskCreateModal
        show={showCreateModal}
        handleClose={() => setShowCreateModal(false)}
        refreshTasks={fetchTasks}
      />

      {/* Edit Task Modal */}
      {currentTask && (
        <TaskEditModal
          show={showEditModal}
          handleClose={() => setShowEditModal(false)}
          task={currentTask}
          refreshTasks={fetchTasks}
        />
      )}
    </Container>
  )
}

export default TaskList
