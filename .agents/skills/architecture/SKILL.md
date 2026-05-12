---
name: architecture
description: App Architecture & Coding Conventions. Enforce this skill to ensure the app is built with a clean, maintainable architecture. Follow all the rules defined in this skill.
---

# Skill: App Architecture & Coding Conventions

## 1. Tech Stack & Rules

- **State Management**: Riverpod 3.0+ (Plain classes, **NO build_runner and riverpod_generator**).
- **Conventions**:
  - Use `AsyncNotifier<T>` for all asynchronous state.
  - Use `Notifier<T>` for synchronous state.
  - Suffix all providers with `Provider` (e.g., `doctorsProvider`).

## 2. Design System Integration (MANDATORY)

- **Buttons**: Use `AppButton` for all actions.
  - Primary action -> `AppButtonVariant.primary`
  - Secondary/Cancel -> `AppButtonVariant.secondary`
- **Inputs**: Use `AppTextField` for all user inputs. Never use `TextField` or `TextFormField` directly.
- **Spacing**: Use `SizedBox(height: 16)` (md) or `24` (lg) for vertical spacing to maintain the "airy" healthcare feel.
- **Rounded corners**: use 12 radius for all rounded corners.
- **Chips**: use container for chips like this code below

```dart
Container(
    decoration: BoxDecoration(
        color: isSelected ? const Color(0xFFE0F2F1) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
            color: isSelected
                ? AppColors.primary
                : AppColors.outline.withOpacity(0.3),
        ),
    ),
    child: Center(
        child: Text(
            slot,
            style: TextStyle(
            color: isSelected ? AppColors.primary : AppColors.textPrimary,
            fontWeight: FontWeight.bold,
            ),
        ),
    ),
),
```

## 3. Data & State Logic (Simplified)

- **Direct Integration**: Place all data fetching logic (http calls, mock data) directly inside the `Notifier` or `AsyncNotifier` methods.
- **Example Pattern**:

  ```dart
  class DoctorsNotifier extends AsyncNotifier<List<Doctor>> {
    @override
    Future<List<Doctor>> build() async {
      // Data logic directly in the provider
      return _fetchDoctors();
    }

    Future<List<Doctor>> _fetchDoctors() async {
      // Mock or HTTP logic here
    }
  }
  ```

## 4. Simplified Folder Structure

/lib
/models # ALL data models (Plain Dart classes)
/core # Global theme, colors, and utils
/features # Organized by feature
/[feature_name]
/[feature_name]/providers # State management AND API/Data logic in one place
/[feature_name]/screens # UI Screens
/[feature_name]/widgets # Feature-specific components
/widgets # Global reusable components (AppButton, AppTextField, etc.)
