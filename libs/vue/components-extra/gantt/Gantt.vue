<template>
  <div class="gantt-container">
    <!-- Table Header -->
    <div class="gantt-header">
      <div class="gantt-task-column">Task</div>
      <div class="gantt-timeline">
        <div
          v-for="n in totalDays"
          :key="n"
          class="gantt-day"
        >
          {{ n }}
        </div>
      </div>
    </div>

    <!-- Table Rows -->
    <div
      v-for="task in table.getRowModel().rows"
      :key="task.id"
      class="gantt-row"
    >
      <!-- Task Name Column -->
      <div class="gantt-task-column">{{ task.original.name }}</div>

      <!-- Gantt Task Bar -->
      <div class="gantt-timeline">
        <div
          ref="dragRefs"
          class="gantt-task-bar"
          :style="{
            left: getTaskPosition(task.original).startOffset * 30 + 'px',
            width: getTaskPosition(task.original).duration * 30 + 'px',
          }"
          @mounted="makeDraggable(task.original)"
        >
          <div
            ref="resizeStartRefs"
            class="resize-handle left"
          ></div>
          {{ task.original.name }}
          <div
            ref="resizeEndRefs"
            class="resize-handle right"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useVueTable, createColumnHelper, getCoreRowModel } from '@tanstack/vue-table';
import { useDraggable } from '@vueuse/core';

interface Task {
  id: number;
  name: string;
  start: string; // ISO format "YYYY-MM-DD"
  end: string;
}

// Props
const props = defineProps<{ tasks: Task[] }>();

// State for tasks (local copy to allow modifications)
const taskList = ref([...props.tasks]);

// Compute timeline range
const startDate = computed(
  () => new Date(Math.min(...taskList.value.map((t) => new Date(t.start).getTime()))),
);
const endDate = computed(
  () => new Date(Math.max(...taskList.value.map((t) => new Date(t.end).getTime()))),
);
const totalDays = computed(() =>
  Math.ceil((endDate.value.getTime() - startDate.value.getTime()) / (1000 * 60 * 60 * 24)),
);

const columnHelper = createColumnHelper<Task>();
const columns = [
  columnHelper.accessor('name', {
    header: 'Task',
    cell: (info) => info.getValue(),
  }),
];

// Create table
const table = useVueTable({
  columns,
  data: taskList.value,
  getCoreRowModel: getCoreRowModel<Task>(),
});

// Compute task bar position
function getTaskPosition(task: Task) {
  const taskStart = new Date(task.start);
  const taskEnd = new Date(task.end);
  const startOffset = (taskStart.getTime() - startDate.value.getTime()) / (1000 * 60 * 60 * 24);
  const duration = (taskEnd.getTime() - taskStart.getTime()) / (1000 * 60 * 60 * 24);
  return { startOffset, duration };
}

// Function to update task date
function updateTaskDate(task: Task, newStartOffset: number, newDuration: number) {
  const newStartDate = new Date(startDate.value);
  newStartDate.setDate(newStartDate.getDate() + newStartOffset);

  const newEndDate = new Date(newStartDate);
  newEndDate.setDate(newEndDate.getDate() + newDuration);

  task.start = newStartDate.toISOString().slice(0, 10);
  task.end = newEndDate.toISOString().slice(0, 10);
}

// Drag & Resize handlers
const dragRefs = new Map<number, HTMLElement>();
const resizeStartRefs = new Map<number, HTMLElement>();
const resizeEndRefs = new Map<number, HTMLElement>();

function makeDraggable(task: Task) {
  // `useDraggable` reports absolute positions, so capture the pointer x on start and
  // derive the horizontal delta on end (vueuse's `Position` has no `deltaX`).
  let dragStartX = 0;
  useDraggable(dragRefs.get(task.id), {
    onStart: ({ x }) => {
      dragStartX = x;
    },
    onEnd: ({ x }) => {
      const daysMoved = Math.round((x - dragStartX) / 30);
      updateTaskDate(
        task,
        getTaskPosition(task).startOffset + daysMoved,
        getTaskPosition(task).duration,
      );
    },
  });

  let resizeStartX = 0;
  useDraggable(resizeStartRefs.get(task.id), {
    onStart: ({ x }) => {
      resizeStartX = x;
    },
    onEnd: ({ x }) => {
      const daysResized = Math.round((x - resizeStartX) / 30);
      updateTaskDate(
        task,
        getTaskPosition(task).startOffset + daysResized,
        getTaskPosition(task).duration - daysResized,
      );
    },
  });

  let resizeEndX = 0;
  useDraggable(resizeEndRefs.get(task.id), {
    onStart: ({ x }) => {
      resizeEndX = x;
    },
    onEnd: ({ x }) => {
      const daysResized = Math.round((x - resizeEndX) / 30);
      updateTaskDate(
        task,
        getTaskPosition(task).startOffset,
        getTaskPosition(task).duration + daysResized,
      );
    },
  });
}
</script>

<style scoped>
.gantt-container {
  display: flex;
  flex-direction: column;
  border: 1px solid #ccc;
  overflow-x: auto;
}

.gantt-header {
  display: flex;
  font-weight: bold;
  background: #f8f8f8;
}

.gantt-task-column {
  width: 150px;
  padding: 8px;
  border-right: 1px solid #ddd;
}

.gantt-timeline {
  display: flex;
  flex-grow: 1;
  position: relative;
}

.gantt-day {
  width: 30px;
  text-align: center;
  border-right: 1px solid #ddd;
  font-size: 12px;
  color: #555;
}

.gantt-row {
  display: flex;
  align-items: center;
}

.gantt-task-bar {
  position: absolute;
  height: 30px;
  background: #007bff;
  color: white;
  text-align: center;
  border-radius: 4px;
  line-height: 30px;
  cursor: grab;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.resize-handle {
  width: 8px;
  height: 100%;
  background: #333;
  cursor: ew-resize;
}

.resize-handle.left {
  position: absolute;
  left: 0;
}

.resize-handle.right {
  position: absolute;
  right: 0;
}
</style>
