import { useState } from "react";
import { Modal, Button, Form } from "react-bootstrap";
import { createTask } from "../services/api";
import PropTypes from "prop-types";

const TaskCreateModal = ({ show, handleClose, refreshTasks }) => {
  const [form, setForm] = useState({
    title: "",
    description: "",
    dueDate: "",
    task_status: "pending",
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState("");

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setError("");

    try {
      await createTask(form); // Call the API to create a task
      setForm({
        title: "",
        description: "",
        dueDate: "",
        task_status: "pending",
      });
      handleClose(); // Close the modal
      refreshTasks(); // Refresh the task list
    } catch (err) {
      console.error("Error creating task:", err);
      setError("Failed to create task. Please try again.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleCancel = () => {
    setForm({
      title: "",
      description: "",
      dueDate: "",
      task_status: "pending",
    }); // Reset the form
    setError(""); // Clear any errors
    handleClose(); // Close the modal
  };

  return (
    <Modal show={show} onHide={handleCancel}>
      <Form onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>Create Task</Modal.Title>
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
          <Button variant="secondary" onClick={handleCancel} disabled={isSubmitting}>
            Cancel
          </Button>
          <Button variant="primary" type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Creating..." : "Create Task"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
};

TaskCreateModal.propTypes = {
  show: PropTypes.bool.isRequired,
  handleClose: PropTypes.func.isRequired,
  refreshTasks: PropTypes.func.isRequired,
};

export default TaskCreateModal;
