# No Native Browser Dialogs — Use Styled Modal Popups

## Rule

**NEVER** use `window.confirm()`, `window.alert()`, or `window.prompt()` in this project.

All user-facing confirmation dialogs, warnings, and prompts **MUST** use custom styled modal popups that match the application's design system.

## Why

Native browser dialogs (`window.confirm`, `window.alert`, `window.prompt`):
- Look ugly and break the premium UI aesthetic
- Cannot be styled or themed
- Display "localhost says" which looks unprofessional
- Block the JavaScript thread
- Cannot show icons, loading spinners, or rich content

## How to Implement a Confirmation Modal

Follow this pattern used throughout the project:

### 1. Add reactive state in `<script setup>`

```js
const showMyConfirmModal = ref(false);
const myModalTarget = ref(null); // optional: store context data

const openMyConfirmModal = (data) => {
  myModalTarget.value = data;
  showMyConfirmModal.value = true;
};

const confirmMyAction = async () => {
  // perform the action
  showMyConfirmModal.value = false;
  myModalTarget.value = null;
};
```

### 2. Add modal template

```html
<div v-if="showMyConfirmModal" class="no-print fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 text-slate-900">
    <!-- Icon -->
    <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
      <Trash2 class="w-6 h-6" />
    </div>
    <!-- Title & Description -->
    <div class="text-center">
      <h3 class="text-base font-extrabold text-slate-900">Confirm Action?</h3>
      <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Description of what will happen.</p>
    </div>
    <!-- Action Buttons -->
    <div class="flex items-center space-x-2.5 mt-6">
      <button type="button" @click="showMyConfirmModal = false"
        class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
        Cancel
      </button>
      <button type="button" @click="confirmMyAction"
        class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition cursor-pointer">
        Confirm
      </button>
    </div>
  </div>
</div>
```

### 3. Color conventions for modal icon backgrounds

| Action Type         | Icon BG Color       | Button Color        |
|---------------------|---------------------|---------------------|
| Destructive/Delete  | `bg-rose-100`       | `bg-rose-600`       |
| Success/Submit      | `bg-emerald-100`    | `bg-emerald-600`    |
| Physical/Campus     | `bg-indigo-100`     | `bg-indigo-600`     |
| Warning/Follow-up   | `bg-amber-100`      | `bg-amber-600`      |
| Info/Neutral        | `bg-blue-100`       | `bg-blue-600`       |

## Enforcement

If you encounter any `window.confirm`, `window.alert`, or `window.prompt` during code review or editing, replace it with a styled modal popup following the pattern above.
