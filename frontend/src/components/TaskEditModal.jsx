// src/components/TaskEditModal.jsx

import { useState, useEffect } from "react"
import { Modal, Button, Form } from "react-bootstrap"
import { updateTask } from "../services/api"
import PropTypes from "prop-types";

const TaskEditModal = ({ show, handleClose, task, refreshTasks }) => {
  const [form, setForm] = useState({
    title: "",
    description: "",
    dueDate: "",
    task_status: "pending",
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [error, setError] = useState("")

  useEffect(() => {
    if (task) {
      setForm({
        title: task.title,
        description: task.description,
        dueDate: task.dueDate,
        task_status: task.task_status,
      })
    }
  }, [task])

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setIsSubmitting(true)
    setError("")
    try {
      await updateTask(task.taskId, form)
      handleClose()
      refreshTasks()
    } catch (err) {
      console.error("Error updating task:", err)
      setError("Failed to update task. Please try again.")
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <Modal show={show} onHide={handleClose}>
      <Form onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>Edit Task</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          {error && <div className="alert alert-danger">{error}</div>}
          <Form.Group className="mb-3" controlId="formTitle">
            <Form.Label>Title</Form.Label>
            <Form.Control
              type="text"
              name="title"
              value={form.title}
              onChange={handleChange}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3" controlId="formDescription">
            <Form.Label>Description</Form.Label>
            <Form.Control
              as="textarea"
              rows={3}
              name="description"
              value={form.description}
              onChange={handleChange}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3" controlId="formDueDate">
            <Form.Label>Due Date</Form.Label>
            <Form.Control
              type="date"
              name="dueDate"
              value={form.dueDate}
              onChange={handleChange}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3" controlId="formStatus">
            <Form.Label>Status</Form.Label>
            <Form.Select
              name="task_status"
              value={form.task_status}
              onChange={handleChange}
            >
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </Form.Select>
          </Form.Group>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="secondary" onClick={handleClose} disabled={isSubmitting}>
            Cancel
          </Button>
          <Button variant="primary" type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Updating..." : "Update Task"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  )
}

TaskEditModal.propTypes = {
  show: PropTypes.bool.isRequired,
  handleClose: PropTypes.func.isRequired,
  task: PropTypes.shape({
    title: PropTypes.string,
    description: PropTypes.string,
    dueDate: PropTypes.string,
    task_status: PropTypes.string,
    taskId: PropTypes.number,
  }),
  refreshTasks: PropTypes.func.isRequired,
};

export default TaskEditModal
