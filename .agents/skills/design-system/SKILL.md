---
name: design-system
description: Design System for Clinical Clarity. Use this skill to create all UI components for the app. follow the design tokens and components defined in this skill.
---

# Skill: Clinical Clarity Design System

This skill defines the "Clinical Clarity" design system for Flutter. It includes core colors and reusable widgets to ensure a premium, medical-focused aesthetic.

## 1. Design Tokens (AppColors)

File Path: `lib/core/app_colors.dart`

```dart
import 'package:flutter/material.dart';

class AppColors {
  static const Color primary = Color(0xFF00796B);
  static const Color secondary = Color(0xFF26A69A);
  static const Color background = Color(0xFFF6FAF8);
  static const Color surface = Colors.white;
  static const Color textPrimary = Color(0xFF212121);
  static const Color textSecondary = Color(0xFF6E7A76);
  static const Color onPrimary = Colors.white;
  static const Color error = Color(0xFFBA1A1A);
  static const Color outline = Color(0xFFBDC9C5);
}
```

## 2. Core Components

### AppButton

File Path: `lib/widgets/app_button.dart`
Variants: `primary`, `secondary`, `outline`.

```dart
import 'package:flutter/material.dart';
import '../core/app_colors.dart';

enum AppButtonVariant { primary, secondary, outline }

class AppButton extends StatelessWidget {
  final String text;
  final VoidCallback? onPressed;
  final AppButtonVariant variant;
  final bool isLoading;
  final IconData? icon;
  final IconData? suffixIcon;
  final double? width;
  final double height;

  const AppButton({
    super.key,
    required this.text,
    this.onPressed,
    this.variant = AppButtonVariant.primary,
    this.isLoading = false,
    this.icon,
    this.suffixIcon,
    this.width,
    this.height = 52,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    Widget content = Row(
      mainAxisSize: MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        if (isLoading) ...[
          SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              valueColor: AlwaysStoppedAnimation<Color>(
                variant == AppButtonVariant.outline ? AppColors.primary : Colors.white,
              ),
            ),
          ),
          const SizedBox(width: 12),
        ] else if (icon != null) ...[
          Icon(icon, size: 20),
          const SizedBox(width: 8),
        ],
        Text(
          text,
          style: theme.textTheme.labelLarge?.copyWith(
            color: _getTextColor(),
            fontWeight: FontWeight.w600,
          ),
        ),
        if (suffixIcon != null && !isLoading) ...[
          const SizedBox(width: 8),
          Icon(suffixIcon, size: 20),
        ],
      ],
    );

    ButtonStyle style;
    switch (variant) {
      case AppButtonVariant.primary:
        style = ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          padding: const EdgeInsets.symmetric(horizontal: 24),
        );
        break;
      case AppButtonVariant.secondary:
        style = ElevatedButton.styleFrom(
          backgroundColor: AppColors.secondary.withOpacity(0.12),
          foregroundColor: AppColors.primary,
          elevation: 0,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          padding: const EdgeInsets.symmetric(horizontal: 24),
        ).copyWith(
          overlayColor: WidgetStateProperty.all(AppColors.primary.withOpacity(0.08)),
        );
        break;
      case AppButtonVariant.outline:
        style = OutlinedButton.styleFrom(
          foregroundColor: AppColors.primary,
          side: const BorderSide(color: AppColors.primary, width: 2),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          padding: const EdgeInsets.symmetric(horizontal: 24),
        );
        break;
    }

    Widget button = variant == AppButtonVariant.outline
        ? OutlinedButton(onPressed: isLoading ? null : onPressed, style: style, child: content)
        : ElevatedButton(onPressed: isLoading ? null : onPressed, style: style, child: content);

    return SizedBox(width: width, height: height, child: button);
  }

  Color _getTextColor() {
    switch (variant) {
      case AppButtonVariant.primary: return Colors.white;
      default: return AppColors.primary;
    }
  }
}
```

### AppTextField

File Path: `lib/widgets/app_text_field.dart`
Features: Label above field, consistent border.

```dart
import 'package:flutter/material.dart';
import '../core/app_colors.dart';

class AppTextField extends StatelessWidget {
  final String? label;
  final String? hintText;
  final IconData? prefixIcon;
  final Widget? suffixIcon;
  final TextEditingController? controller;
  final bool obscureText;
  final TextInputType? keyboardType;
  final ValueChanged<String>? onChanged;
  final FormFieldValidator<String>? validator;
  final int? maxLines;

  const AppTextField({
    super.key,
    this.label,
    this.hintText,
    this.prefixIcon,
    this.suffixIcon,
    this.controller,
    this.obscureText = false,
    this.keyboardType,
    this.onChanged,
    this.validator,
    this.maxLines = 1,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        if (label != null) ...[
          Text(label!, style: theme.textTheme.labelLarge?.copyWith(color: AppColors.textPrimary, fontWeight: FontWeight.w600)),
          const SizedBox(height: 8),
        ],
        TextFormField(
          controller: controller,
          obscureText: obscureText,
          keyboardType: keyboardType,
          onChanged: onChanged,
          validator: validator,
          maxLines: maxLines,
          style: theme.textTheme.bodyMedium,
          decoration: InputDecoration(
            hintText: hintText,
            hintStyle: theme.textTheme.bodyMedium?.copyWith(color: AppColors.textSecondary.withOpacity(0.5)),
            prefixIcon: prefixIcon != null ? Icon(prefixIcon, color: AppColors.textSecondary, size: 20) : null,
            suffixIcon: suffixIcon,
            filled: true,
            fillColor: AppColors.surface,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFCFD8DC))),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFCFD8DC))),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.primary, width: 2)),
            errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.error)),
            focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.error, width: 2)),
          ),
        ),
      ],
    );
  }
}
```

## 3. Mandatory Implementation Rules

1. **Strict Usage**: NEVER use standard `ElevatedButton`, `OutlinedButton`, or `TextField` directly in screen files. Always use `AppButton` and `AppTextField`. If the widget doesnt exist, create it and add it to the `lib/widgets/` folder.
2. **Variants**:
   - Use `AppButtonVariant.primary` for the main action on a screen (e.g., "Confirm").
   - Use `AppButtonVariant.secondary` for alternative actions (e.g., "Cancel").
   - Use `AppButtonVariant.outline` for low-priority actions (e.g., "Skip").
3. **Styling**: Always maintain the 12px corner radius for interactive elements.
4. **Layout**: Section margins should lean towards 24px (lg) or 32px (xl) to maintain the "airy" healthcare aesthetic.

colors:

surface: '#f6faf8'
surface-dim: '#d7dbd8'
surface-bright: '#f6faf8'
surface-container-lowest: '#ffffff'
surface-container-low: '#f0f4f2'
surface-container: '#ebefec'
surface-container-high: '#e5e9e7'
surface-container-highest: '#dfe3e1'
on-surface: '#181d1b'
on-surface-variant: '#3e4946'
inverse-surface: '#2d3130'
inverse-on-surface: '#eef2ef'
outline: '#6e7a76'
outline-variant: '#bdc9c5'
surface-tint: '#006b5e'
primary: '#005e53'
on-primary: '#ffffff'
primary-container: '#00796b'
on-primary-container: '#a1feec'
inverse-primary: '#7ad7c6'
secondary: '#006a62'
on-secondary: '#ffffff'
secondary-container: '#81f3e5'
on-secondary-container: '#006f66'
tertiary: '#843f29'
on-tertiary: '#ffffff'
tertiary-container: '#a2563f'
on-tertiary-container: '#ffe8e2'
error: '#ba1a1a'
on-error: '#ffffff'
error-container: '#ffdad6'
on-error-container: '#93000a'
primary-fixed: '#97f3e2'
primary-fixed-dim: '#7ad7c6'
on-primary-fixed: '#00201b'
on-primary-fixed-variant: '#005047'
secondary-fixed: '#84f5e8'
secondary-fixed-dim: '#66d9cc'
on-secondary-fixed: '#00201d'
on-secondary-fixed-variant: '#005049'
tertiary-fixed: '#ffdbd1'
tertiary-fixed-dim: '#ffb59f'
on-tertiary-fixed: '#3a0a00'
on-tertiary-fixed-variant: '#75331f'
background: '#f6faf8'
on-background: '#181d1b'
surface-variant: '#dfe3e1'
typography:
h1:
fontFamily: Inter
fontSize: 32px
fontWeight: '700'
lineHeight: '1.2'
letterSpacing: -0.02em
h2:
fontFamily: Inter
fontSize: 24px
fontWeight: '600'
lineHeight: '1.3'
letterSpacing: -0.01em
h3:
fontFamily: Inter
fontSize: 20px
fontWeight: '600'
lineHeight: '1.4'
letterSpacing: '0'
body-lg:
fontFamily: Inter
fontSize: 18px
fontWeight: '400'
lineHeight: '1.6'
letterSpacing: '0'
body-md:
fontFamily: Inter
fontSize: 16px
fontWeight: '400'
lineHeight: '1.5'
letterSpacing: '0'
body-sm:
fontFamily: Inter
fontSize: 14px
fontWeight: '400'
lineHeight: '1.5'
letterSpacing: '0'
label-caps:
fontFamily: Inter
fontSize: 12px
fontWeight: '700'
lineHeight: '1'
letterSpacing: 0.05em
button:
fontFamily: Inter
fontSize: 16px
fontWeight: '600'
lineHeight: '1'
letterSpacing: 0.01em
rounded:
sm: 0.25rem
DEFAULT: 0.5rem
md: 0.75rem
lg: 1rem
xl: 1.5rem
full: 9999px
spacing:
unit: 4px
xs: 4px
sm: 8px
md: 16px
lg: 24px
xl: 32px
xxl: 48px
margin-mobile: 16px
margin-desktop: 64px
gutter: 16px

---

## Brand & Style

The design system is anchored in the concept of "Clinical Clarity"—a visual language that balances medical precision with human empathy. The target audience includes patients seeking reliable care and healthcare providers requiring efficient workflows.

The style utilizes **Modern Corporate** principles with a heavy emphasis on **Minimalism**. It avoids unnecessary decorative elements to focus on readability and accessibility. By utilizing significant whitespace and a restrained color palette, the system evokes a sense of calm and competence. The interface feels "sterile" in its cleanliness but "warm" through its soft geometry and generous breathing room, ensuring users feel both safe and cared for during their digital health journey.

## Colors

The palette is dominated by **Deep Teal**, a color chosen for its association with health, stability, and professional expertise.

- **Primary (#00796B):** Used for key call-to-actions, active states, and brand-heavy components.
- **Secondary (#26A69A):** A lighter teal for accents, progress indicators, and supporting data visualizations.
- **Background (#F5F5F5):** A soft grey that reduces eye strain compared to pure white, providing a sophisticated backdrop for white surface cards.
- **Text:** Dark Grey (#212121) is used for high-contrast legibility in body and headings, while White is reserved for text on primary-colored backgrounds.
- **Status Colors:** Use standard semantic colors (Red for alerts, Amber for warnings, Blue for info) but desaturate them slightly to fit the professional tone.

## Typography

This design system utilizes **Inter** for its exceptional legibility and neutral, systematic character. The type scale is optimized for information density and readability in a clinical context.

Headlines use a tighter letter-spacing and heavier weights to establish a clear hierarchy. Body text is prioritized for comfort, utilizing a generous line-height to assist users who may be viewing the app under stress or with visual impairments. Use "body-md" for general interface text and "body-sm" for secondary metadata or legal disclaimers.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy on desktop (12 columns, 1140px max-width) and a **Fluid Grid** on mobile (4 columns).

The spacing rhythm is built on a 4px base unit. To achieve the "friendly and airy" aesthetic requested, always lean toward larger increments (lg and xl) for section padding and vertical margins. Negative space is not "empty" space; it is a tool used to group related medical information and reduce cognitive load. Elements should never feel cramped; if a screen feels busy, increase the container padding rather than shrinking the content.

## Elevation & Depth

The design system employs **Tonal Layers** supplemented by **Ambient Shadows** to create a structured hierarchy.

- **Level 0 (Background):** Soft Grey (#F5F5F5) - the base canvas.
- **Level 1 (Cards/Surfaces):** Pure White (#FFFFFF) - these represent the primary interactive containers. They feature a very soft, diffused shadow (0px 4px 20px rgba(0, 0, 0, 0.04)) to subtly lift them from the background.
- **Level 2 (Modals/Popovers):** Pure White (#FFFFFF) - these use a more pronounced shadow (0px 10px 30px rgba(0, 0, 0, 0.08)) to indicate they are at the top of the stack.

Avoid using heavy borders. Instead, use these subtle elevation changes to define boundaries and priority.

## Shapes

The shape language is defined by a consistent **Rounded (0.5rem / 8px base)** logic, but for this specific healthcare system, we scale up to `rounded-xl` (16px) for major containers and cards.

- **Primary Cards:** 16px corner radius.
- **Buttons & Inputs:** 12px corner radius.
- **Small Elements (Chips/Tags):** 8px or fully rounded (pill).

These large radii soften the "clinical" feel, making the technology feel approachable and modern rather than rigid or intimidating.

## Components

- **Cards:** The workhorse of the design system. All cards are white, have 16px rounded corners, and include 24px of internal padding. They should be used to group patient data, appointment details, or doctor profiles.
- **Chips/Tags:** Used for medical categories or status (e.g., "Confirmed," "Urgent"). Use a light tint of the status color for the background and a darkened version for the text.
- **Telemedicine Specifics:**
  - **Video Feed Container:** Large 16px rounded corners with a subtle inner glow to signify the active stream.
  - **Appointment Tracker:** A vertical timeline component using the primary Teal for the active path and Soft Grey for pending steps.
  - **Prescription List:** A high-contrast list item with a leading icon and trailing chevron for deep-dive details.
