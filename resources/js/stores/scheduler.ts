import { defineStore } from 'pinia'
import type { Event, SchedulerState, StartOfWeek } from '@/types/scheduler'

export const useSchedulerStore = defineStore('scheduler', {
  state: (): SchedulerState & { weekStartsOn: StartOfWeek; selectedDate: Date } => ({
    events: [],
    weekStartsOn: 'sunday',
    selectedDate: new Date()
  }),

  actions: {
    addEvent(event: Event) {
      this.events.push(event)
    },

    updateEvent(event: Event) {
      const index = this.events.findIndex(e => e.id === event.id)
      if (index !== -1) {
        this.events[index] = event
      }
    },

    deleteEvent(id: string) {
      this.events = this.events.filter(e => e.id !== id)
    },

    setEvents(events: Event[]) {
      this.events = events
    },

    setWeekStartsOn(day: StartOfWeek) {
      this.weekStartsOn = day
    },

    setSelectedDate(date: Date) {
      this.selectedDate = date
    }
  },

  getters: {
    getDaysInMonth: (state) => (month: number, year: number) => {
      return Array.from(
        { length: new Date(year, month + 1, 0).getDate() },
        (_, index) => ({
          day: index + 1,
          events: []
        })
      )
    },

    getEventsForDay: (state) => (day: number, currentDate: Date) => {
      return state.events.filter((event) => {
        const eventStart = new Date(event.startDate)
        const eventEnd = new Date(event.endDate)

        const startOfDay = new Date(currentDate)
        startOfDay.setDate(day)
        startOfDay.setHours(0, 0, 0, 0)

        const endOfDay = new Date(currentDate)
        endOfDay.setDate(day + 1)
        endOfDay.setHours(0, 0, 0, 0)

        const isSameDay =
          eventStart.getDate() === day &&
          eventStart.getMonth() === currentDate.getMonth() &&
          eventStart.getFullYear() === currentDate.getFullYear()

        const isSpanningDay = eventStart < endOfDay && eventEnd >= startOfDay

        return isSameDay || isSpanningDay
      })
    },

    getDaysInWeek: (state) => (week: number, year: number) => {
      const startDay = state.weekStartsOn === 'sunday' ? 0 : 1
      const janFirst = new Date(year, 0, 1)
      const janFirstDayOfWeek = janFirst.getDay()

      const weekStart = new Date(janFirst)
      weekStart.setDate(
        janFirst.getDate() +
          (week - 1) * 7 +
          ((startDay - janFirstDayOfWeek + 7) % 7)
      )

      const days = []
      for (let i = 0; i < 7; i++) {
        const day = new Date(weekStart)
        day.setDate(day.getDate() + i)
        days.push(day)
      }

      return days
    },

    getWeekNumber: () => (date: Date) => {
      const d = new Date(
        Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())
      )
      d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7))
      const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))
      const weekNo = Math.ceil(
        ((d.getTime() - yearStart.getTime()) / 86400000 + 1) / 7
      )
      return weekNo
    },

    getDayName: () => (day: number) => {
      const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
      return days[day]
    }
  }
})
