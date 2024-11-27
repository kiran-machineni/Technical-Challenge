// src/App.jsx

import React from "react"
import { Routes, Route, Link, Navigate } from "react-router-dom"
import TaskList from "./pages/TaskList"

function App() {
  return (
    <div>
      <nav className="navbar navbar-expand-lg navbar-light bg-light">
        <div className="container">
          <Link className="navbar-brand" to="/">
            PHP Task Manager
          </Link>
          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon"></span>
          </button>
          {/* Future Navbar Items Can Go Here */}
        </div>
      </nav>

      <Routes>
        <Route path="/" element={<TaskList />} />
        {/* Redirect any unknown routes to the main page */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </div>
  )
}

export default App
