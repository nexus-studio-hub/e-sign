import type { Shift, DaySchedule, WorkScheduleTemplate, ScheduleFormData } from '@/types/work-schedule'
import { getAllDoctors, getDoctorById } from './doctors'
import type { Doctor } from '@/types/doctor'

const STORAGE_KEY = 'work_schedules'

export const dummyShifts: Shift[] = [
  {
    id: 'shift-1',
    name: 'Shift 1 - Morning',
    start_time: '08:00',
    end_time: '13:00',
    color: '#3B82F6'
  },
  {
    id: 'shift-2',
    name: 'Shift 2 - Afternoon',
    start_time: '13:00',
    end_time: '19:00',
    color: '#10B981'
  }
]

// Use doctors from doctors.ts instead of local dummyDoctors
export const dummyDoctors = getAllDoctors()

const defaultSchedules: WorkScheduleTemplate[] = [
  {
    id: 'schedule-1',
    name: 'Default Weekly Schedule',
    is_active: true,
    shifts: dummyShifts,
    schedules: [
      { day: 'monday', shift_id: 'shift-1', doctor_ids: ['1', '2', '3'] },
      { day: 'monday', shift_id: 'shift-2', doctor_ids: ['4', '5'] },
      { day: 'tuesday', shift_id: 'shift-1', doctor_ids: ['1', '3', '6'] },
      { day: 'tuesday', shift_id: 'shift-2', doctor_ids: ['2', '4', '5'] },
      { day: 'wednesday', shift_id: 'shift-1', doctor_ids: ['2', '3', '4'] },
      { day: 'wednesday', shift_id: 'shift-2', doctor_ids: ['1', '5', '6'] },
      { day: 'thursday', shift_id: 'shift-1', doctor_ids: ['1', '2', '5'] },
      { day: 'thursday', shift_id: 'shift-2', doctor_ids: ['3', '4', '6'] },
      { day: 'friday', shift_id: 'shift-1', doctor_ids: ['3', '4', '6'] },
      { day: 'friday', shift_id: 'shift-2', doctor_ids: ['1', '2', '5'] },
      { day: 'saturday', shift_id: 'shift-1', doctor_ids: ['1', '3'] },
      { day: 'saturday', shift_id: 'shift-2', doctor_ids: ['4', '5'] },
      { day: 'sunday', shift_id: 'shift-1', doctor_ids: ['2', '6'] },
      { day: 'sunday', shift_id: 'shift-2', doctor_ids: ['3', '5'] }
    ],
    created_at: '2024-09-01T00:00:00',
    updated_at: '2024-09-01T00:00:00'
  },
  {
    id: 'schedule-2',
    name: 'Weekend Schedule',
    is_active: false,
    shifts: [
      { id: 'shift-3', name: 'Full Time', start_time: '08:00', end_time: '17:00', color: '#8B5CF6' }
    ],
    schedules: [
      { day: 'saturday', shift_id: 'shift-3', doctor_ids: ['1', '3', '5'] },
      { day: 'sunday', shift_id: 'shift-3', doctor_ids: ['2', '4', '6'] }
    ],
    created_at: '2024-09-05T00:00:00',
    updated_at: '2024-09-05T00:00:00'
  }
]

const loadSchedules = (): WorkScheduleTemplate[] => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (stored) {
      return JSON.parse(stored)
    }
  } catch (e) {
    console.error('Failed to load schedules from localStorage', e)
  }
  return defaultSchedules
}

const saveSchedules = (schedules: WorkScheduleTemplate[]) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(schedules))
  } catch (e) {
    console.error('Failed to save schedules to localStorage', e)
  }
}

let allSchedules = loadSchedules()

export const getAllSchedules = (): WorkScheduleTemplate[] => {
  return allSchedules
}

export const getActiveSchedule = (): WorkScheduleTemplate => {
  return allSchedules.find(s => s.is_active) || allSchedules[0]
}

export const getScheduleById = (id: string): WorkScheduleTemplate | undefined => {
  return allSchedules.find(s => s.id === id)
}

export const createSchedule = (data: ScheduleFormData): WorkScheduleTemplate => {
  if (data.is_active) {
    allSchedules.forEach(s => s.is_active = false)
  }
  
  const newSchedule: WorkScheduleTemplate = {
    id: `schedule-${Date.now()}`,
    name: data.name,
    is_active: data.is_active,
    shifts: data.shifts,
    schedules: data.schedules,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }
  
  allSchedules.push(newSchedule)
  saveSchedules(allSchedules)
  return newSchedule
}

export const updateSchedule = (id: string, data: Partial<ScheduleFormData>): WorkScheduleTemplate | null => {
  const index = allSchedules.findIndex(s => s.id === id)
  if (index === -1) return null
  
  if (data.is_active) {
    allSchedules.forEach(s => s.is_active = false)
  }
  
  allSchedules[index] = {
    ...allSchedules[index],
    ...data,
    updated_at: new Date().toISOString()
  }
  
  saveSchedules(allSchedules)
  return allSchedules[index]
}

export const deleteSchedule = (id: string): boolean => {
  const index = allSchedules.findIndex(s => s.id === id)
  if (index === -1) return false
  
  if (allSchedules[index].is_active) return false
  
  allSchedules.splice(index, 1)
  saveSchedules(allSchedules)
  return true
}

export const setActiveSchedule = (id: string): boolean => {
  allSchedules.forEach(s => s.is_active = false)
  
  const schedule = allSchedules.find(s => s.id === id)
  if (!schedule) return false
  
  schedule.is_active = true
  saveSchedules(allSchedules)
  return true
}

export const getCurrentDayName = (): 'monday' | 'tuesday' | 'wednesday' | 'thursday' | 'friday' | 'saturday' | 'sunday' => {
  const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as const
  const today = new Date()
  return days[today.getDay()]
}

export const getScheduleForDay = (schedule: WorkScheduleTemplate, day: string) => {
  return schedule.schedules.filter(s => s.day === day)
}

export const getDoctorsForShift = (schedule: WorkScheduleTemplate, day: string, shiftId: string): Doctor[] => {
  const daySchedule = schedule.schedules.find(s => s.day === day && s.shift_id === shiftId)
  if (!daySchedule) return []
  
  const allDoctors = getAllDoctors()
  return daySchedule.doctor_ids
    .map(id => allDoctors.find(d => d.id === id))
    .filter(Boolean) as Doctor[]
}
