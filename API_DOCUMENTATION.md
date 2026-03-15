# IT15_BACKEND API Documentation

This file documents all implemented API endpoints, expected request payloads, and response structures.

## Base URL

- Local API base: http://127.0.0.1:8000/api

## Authentication

## POST /login

Authenticates a user and returns a bearer token.

Request body:

```json
{
  "email": "registrar@dollente.edu",
  "password": "password"
}
```

Success response (200):

```json
{
  "message": "Login successful",
  "token": "<sanctum-token>",
  "user": {
    "id": 1,
    "name": "Registrar User",
    "email": "registrar@dollente.edu"
  }
}
```

Error response (401):

```json
{
  "message": "Invalid credentials"
}
```

## Protected Endpoints

All endpoints below require:

- Header: Authorization: Bearer <token>
- Header: Accept: application/json

## Students

## GET /students

Returns all students with related course.

Sample response:

```json
[
  {
    "id": 1,
    "student_number": "UM-0001",
    "first_name": "Maria",
    "last_name": "Santos",
    "email": "maria.santos@example.com",
    "year_level": 1,
    "status": "Enrolled",
    "course": {
      "id": 1,
      "code": "BSIT",
      "name": "BS Information Technology",
      "department": "Computing"
    }
  }
]
```

## POST /students

Creates a student record.

Request body:

```json
{
  "first_name": "Juan",
  "last_name": "Dela Cruz",
  "email": "juan@example.com",
  "gender": "Male",
  "birth_date": "2005-07-15",
  "department": "Computing",
  "course_id": 1,
  "year_level": 1,
  "status": "Enrolled"
}
```

Success response (201): created student object (includes generated student_number).

Validation error example (422):

```json
{
  "message": "Selected course does not belong to the selected department."
}
```

## GET /students/search?q={query}

Searches students by student number, first name, or last name.

Sample response:

```json
[
  {
    "student_id": "UM-0001",
    "full_name": "Maria Santos",
    "year_level": 1,
    "program": {
      "id": 1,
      "code": "BSIT",
      "name": "BS Information Technology",
      "department": "Computing"
    }
  }
]
```

## GET /students/{studentNumber}/enrollment-history

Returns student details and computed subject history by year/term.

Sample response:

```json
{
  "student": {
    "id": 1,
    "student_number": "UM-0001",
    "full_name": "Maria Santos",
    "status": "Enrolled",
    "year_level": 1
  },
  "program": {
    "id": 1,
    "code": "BSIT",
    "name": "BS Information Technology",
    "department": "Computing"
  },
  "current_term": "2nd Semester",
  "history": [
    {
      "year_level": 1,
      "label": "1st Year",
      "is_current": true,
      "total_subjects": 4,
      "total_units": 12,
      "terms": [
        {
          "term": "2nd Semester",
          "subjects": [
            {
              "id": 10,
              "code": "BSIT103",
              "title": "Digital Systems and Productivity (BSIT)",
              "units": 3,
              "term_indicator": "Per Semester"
            }
          ]
        }
      ]
    }
  ]
}
```

## Programs and Courses

## GET /programs

Returns transformed course data as program-style objects for frontend modules.

## POST /programs

Creates a new course/program.

Request body:

```json
{
  "code": "BSCS",
  "name": "Bachelor of Science in Computer Science",
  "department": "Computing",
  "status": "Active"
}
```

## PUT /programs/{course}

Updates an existing program.

## DELETE /programs/{course}

Deletes a program.

Response:

```json
{
  "message": "Course deleted successfully."
}
```

## GET /courses

Returns raw course options for student/enrollment forms.

## Subjects

## GET /subjects

Returns subject offerings and metadata.

Sample response:

```json
[
  {
    "id": 120,
    "code": "BSIT101",
    "title": "Fundamentals of Computing (BSIT)",
    "units": 3,
    "yearLevel": "1st Year",
    "offeredIn": "1st Semester",
    "termIndicator": "Per Semester",
    "programCode": "BSIT",
    "programName": "BS Information Technology",
    "department": "Computing",
    "description": "...",
    "prerequisites": [],
    "createdAt": "2026-03-15"
  }
]
```

## Enrollment

## GET /enrollment/options

Query params:

- student_id (required)
- term (optional)
- year_level (optional)

Example request:

- /enrollment/options?student_id=UM-0001

Sample response:

```json
{
  "student": {
    "id": 1,
    "student_number": "UM-0001",
    "full_name": "Maria Santos",
    "status": "Enrolled",
    "current_year_level": 1,
    "current_course": {
      "id": 1,
      "code": "BSIT",
      "name": "BS Information Technology"
    }
  },
  "selected": {
    "course": {
      "id": 1,
      "code": "BSIT",
      "name": "BS Information Technology"
    },
    "term": "2nd Semester",
    "year_level": 1,
    "label": "1st Year"
  },
  "current_term": "2nd Semester",
  "available_by_year": [
    {
      "year_level": 1,
      "label": "1st Year",
      "subjects": []
    }
  ],
  "available_for_selected_year": []
}
```

## POST /enrollment

Updates student enrollment metadata and returns available subjects for selected term/year.

Request body:

```json
{
  "student_id": "UM-0001"
}
```

Sample response:

```json
{
  "message": "Student enrollment updated successfully.",
  "student": {
    "student_number": "UM-0001",
    "full_name": "Maria Santos",
    "year_level": 1,
    "status": "Enrolled"
  },
  "course": {
    "id": 1,
    "code": "BSIT",
    "name": "BS Information Technology"
  },
  "term": "2nd Semester",
  "available_subjects": []
}
```

## Dashboard

## GET /dashboard/overview

Response:

```json
{
  "students": 120,
  "courses": 20,
  "school_days": 180,
  "average_attendance": 95.4
}
```

## GET /dashboard/enrollment-trend

Response items:

```json
[
  {
    "month": "Mar",
    "enrolled": 25,
    "target": 24
  }
]
```

## GET /dashboard/course-distribution

Response items:

```json
[
  {
    "course": "BS Information Technology",
    "short": "BSIT",
    "students": 200
  }
]
```

## GET /dashboard/attendance-trend

Response items:

```json
[
  {
    "date": "2026-03-15",
    "label": "Mar 15",
    "attendance": 96.5
  }
]
```

## School Days

## GET /school-days

Returns school day records used for attendance views.

## Token Utility

## GET /token-test

Utility endpoint that returns a plain text token for first user. Intended for development/debug only.

## Standard Error Shape

Recommended error shape from backend:

```json
{
  "message": "Human-readable error message"
}
```

Last updated: March 15, 2026
