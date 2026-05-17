# Exercise Management Module

Complete CRUD exercise management system for FitTrack application.

## Overview

The Exercise Management module provides a full-featured interface for managing workout exercises with comprehensive filtering, validation, and a modern UI built with shadcn-vue components.

## Database Schema

**Table: exercises**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint | ✗ | - | Primary key |
| name | string | ✗ | - | Exercise name (unique) |
| slug | string | ✗ | - | URL-friendly slug (auto-generated) |
| description | text | ✓ | null | Exercise description |
| muscle_group | string | ✗ | - | Target muscle group |
| equipment | string | ✓ | null | Required equipment |
| difficulty | string | ✗ | Beginner | Difficulty level |
| instructions | text | ✓ | null | Step-by-step instructions |
| video_url | string | ✓ | null | Tutorial video URL |
| image | string | ✓ | null | Exercise image URL |
| is_active | boolean | ✗ | true | Active status |
| created_at | timestamp | ✓ | - | Creation timestamp |
| updated_at | timestamp | ✓ | - | Last update timestamp |

## API Routes

```
GET    /exercises                 - List all exercises
POST   /exercises                 - Create new exercise
PUT    /exercises/{exercise}      - Update exercise
DELETE /exercises/{exercise}      - Delete exercise
```

## Architecture

### Backend Structure

```
app/
├── Models/
│   └── Exercise.php                    # Eloquent model
├── Enums/
│   ├── MuscleGroupEnum.php             # 12 muscle groups
│   ├── EquipmentEnum.php               # 8 equipment types
│   └── DifficultyEnum.php              # 3 difficulty levels
├── Actions/Exercises/
│   ├── ListExercisesAction.php          # Filtering & retrieval logic
│   ├── StoreExerciseAction.php          # Create logic
│   ├── UpdateExerciseAction.php         # Update logic
│   └── DestroyExerciseAction.php        # Delete logic
├── Http/
│   ├── Controllers/Exercises/
│   │   ├── ListExercisesController.php
│   │   ├── StoreExerciseController.php
│   │   ├── UpdateExerciseController.php
│   │   └── DestroyExerciseController.php
│   ├── Requests/Exercises/
│   │   ├── StoreExerciseRequest.php     # Create validation
│   │   └── UpdateExerciseRequest.php    # Update validation
│   └── Resources/
│       └── ExerciseResource.php         # JSON transformation
└── database/migrations/
    └── 2026_05_15_212445_create_exercises_table.php
```

### Frontend Structure

```
resources/js/
├── pages/exercises/
│   ├── ListExercises.vue                # Main listing page
│   └── components/
│       ├── CreateExerciseSheet.vue      # Create modal form
│       └── EditExerciseSheet.vue        # Edit modal form
├── routes/exercises/
│   └── index.ts                         # Wayfinder route definitions
└── components/
    └── AppSidebar.vue                   # Updated with Exercises link
```

## Features

### CRUD Operations
- ✓ **Create**: Add new exercises with full form validation
- ✓ **Read**: List all exercises with card-based layout
- ✓ **Update**: Edit exercise details in modal form
- ✓ **Delete**: Remove exercises with confirmation

### User Interface
- **Sheet Modals**: Clean create/edit forms using shadcn-vue Sheet component
- **Card Layout**: Responsive card-based exercise listing
- **Color Coding**: Difficulty levels have distinct color badges
- **Icons**: Dumbbell icon in sidebar navigation
- **Responsive Design**: Works on mobile, tablet, and desktop

### Form Features
- **Select Dropdowns**: Muscle groups, equipment, and difficulty levels
- **Text Fields**: Name, video URL, image URL
- **Text Areas**: Description and instructions
- **Validation**: Client-side with server-side enforcement
- **Loading States**: Disabled buttons during submission
- **Error Messages**: Display validation errors inline
- **Success Notifications**: Flash messages on successful operations

### Filtering & Search
The `ListExercisesAction` supports filtering by:
- Search text (name, description)
- Muscle group
- Equipment type
- Difficulty level
- Active status

## Muscle Groups

- Chest
- Back
- Shoulders
- Biceps
- Triceps
- Forearms
- Abs
- Quadriceps
- Hamstrings
- Glutes
- Calves
- Full Body

## Equipment Types

- Dumbbell
- Barbell
- Machine
- Cable
- Bodyweight
- Resistance Band
- Kettlebell
- Smith Machine

## Difficulty Levels

- **Beginner** (Blue) - Suitable for newcomers
- **Intermediate** (Yellow) - Moderate difficulty
- **Advanced** (Red) - High difficulty

## Validation Rules

### Create Exercise
```php
'name' => ['required', 'string', 'max:255', 'unique:exercises']
'description' => ['nullable', 'string']
'muscle_group' => ['required', 'string', 'in:Chest,Back,...']
'equipment' => ['nullable', 'string', 'in:Dumbbell,Barbell,...']
'difficulty' => ['required', 'string', 'in:Beginner,Intermediate,Advanced']
'instructions' => ['nullable', 'string']
'video_url' => ['nullable', 'string', 'url']
'image' => ['nullable', 'string']
'is_active' => ['nullable', 'boolean']
```

### Update Exercise
Same as create, but name is unique excluding the current exercise ID.

## Key Implementation Details

### Slug Generation
Exercise names are automatically converted to URL-friendly slugs using Laravel's `Str::slug()`:
```
"Bench Press" → "bench-press"
```

### Resource Transformation
The `ExerciseResource` transforms Exercise models into a consistent JSON format for the frontend.

### Thin Controllers
Controllers delegate all business logic to Actions, keeping them clean and testable:
```php
// In controller:
$exercise = $action->execute($validated);

// Not:
// Complex logic in controller
```

### Actions Pattern
Each action has a single responsibility:
- `ListExercisesAction`: Query building with filters
- `StoreExerciseAction`: Exercise creation logic
- `UpdateExerciseAction`: Exercise update logic
- `DestroyExerciseAction`: Exercise deletion logic

## TypeScript Support

All Vue components use TypeScript with proper type definitions:
```typescript
interface Exercise {
    id: number;
    name: string;
    description?: string;
    muscle_group: string;
    equipment?: string;
    difficulty: string;
    instructions?: string;
    video_url?: string;
    image?: string;
    is_active: boolean;
}
```

## Testing

Comprehensive test file: `tests/Feature/ExerciseTest.php`

Tests cover:
- Table existence
- Model creation
- Model updates
- Model deletion
- Fillable fields
- Type casting

Run tests:
```bash
php artisan test tests/Feature/ExerciseTest.php
```

## UI Components Used

### shadcn-vue
- Sheet (modals)
- Button
- Input
- Label
- Select
- Textarea

### lucide-vue-next
- Dumbbell (sidebar icon)

### Tailwind CSS
- Responsive grid layouts
- Card styling
- Color utilities for difficulty badges
- Dark mode support

## Integration Points

### Sidebar Navigation
The Exercises module is automatically registered in `AppSidebar.vue` with:
- Icon: Dumbbell
- Label: Exercícios
- Route: `/exercises`

### Wayfinder Routes
Type-safe route generation in `resources/js/routes/exercises/index.ts`:
```typescript
import { list, store, update, destroy } from '@/routes/exercises';

list()           // GET /exercises
store()          // POST /exercises
update(id)       // PUT /exercises/{id}
destroy(id)      // DELETE /exercises/{id}
```

## Future Enhancements

Potential additions:
- Exercise images upload
- Video integration
- Exercise variants/progressions
- Workout program templates
- Exercise history/tracking
- User favorites
- Exercise ratings/reviews
- Exercise tags/categories
- Bulk operations

## Notes

- All dates are timestamps (created_at, updated_at)
- Names are unique to prevent duplicates
- Slugs are auto-generated from names
- Active status allows soft-disabling exercises
- All forms use Inertia's `useForm` hook
- Success messages displayed as flash notifications
- Responsive design with Tailwind CSS
- Portuguese language labels (Exercícios)

