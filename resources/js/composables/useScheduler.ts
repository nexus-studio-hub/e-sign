import { computed } from 'vue'
import { useSchedulerStore } from '@/stores/scheduler'
import type { Event, Handlers, Getters, EventStyling } from '@/types/scheduler'

export function useScheduler() {
  const store = useSchedulerStore()

  const handleEventStyling = (
    event: Event,
    dayEvents: Event[],
    periodOptions?: {
      eventsInSamePeriod?: number
      periodIndex?: number
      adjustForPeriod?: boolean
    }
  ): EventStyling => {
    // Find overlapping events
    const eventsOnHour = dayEvents.filter((e) => {
      if (e.id === event.id) return false

      const eStart = e.startDate instanceof Date ? e.startDate.getTime() : new Date(e.startDate).getTime()
      const eEnd = e.endDate instanceof Date ? e.endDate.getTime() : new Date(e.endDate).getTime()
      const eventStart = event.startDate instanceof Date ? event.startDate.getTime() : new Date(event.startDate).getTime()
      const eventEnd = event.endDate instanceof Date ? event.endDate.getTime() : new Date(event.endDate).getTime()

      return eStart < eventEnd && eEnd > eventStart
    })

    const allEventsInRange = [event, ...eventsOnHour]
    allEventsInRange.sort((a, b) => {
      const aStart = a.startDate instanceof Date ? a.startDate.getTime() : new Date(a.startDate).getTime()
      const bStart = b.startDate instanceof Date ? b.startDate.getTime() : new Date(b.startDate).getTime()
      return aStart - bStart
    })

    const useCustomPeriod = periodOptions?.adjustForPeriod &&
      periodOptions.eventsInSamePeriod !== undefined &&
      periodOptions.periodIndex !== undefined

    let numEventsOnHour = useCustomPeriod ? periodOptions!.eventsInSamePeriod! : allEventsInRange.length
    let indexOnHour = useCustomPeriod ? periodOptions!.periodIndex! : allEventsInRange.indexOf(event)

    if (numEventsOnHour === 0 || indexOnHour === -1) {
      numEventsOnHour = 1
      indexOnHour = 0
    }

    let eventHeight = 0
    let maxHeight = 0
    let eventTop = 0

    if (event.startDate instanceof Date && event.endDate instanceof Date) {
      const startTime = event.startDate.getHours() * 60 + event.startDate.getMinutes()
      const endTime = event.endDate.getHours() * 60 + event.endDate.getMinutes()
      const diffInMinutes = endTime - startTime

      eventHeight = (diffInMinutes / 60) * 64

      const eventStartHour = event.startDate.getHours() + event.startDate.getMinutes() / 60
      const dayEndHour = 24

      maxHeight = Math.max(0, (dayEndHour - eventStartHour) * 64)
      eventHeight = Math.min(eventHeight, maxHeight)
      eventTop = eventStartHour * 64
    }

    const widthPercentage = Math.min(95 / Math.max(numEventsOnHour, 1), 95)
    const leftPosition = indexOnHour * (widthPercentage + 1)
    const safeLeftPosition = Math.min(leftPosition, 100 - widthPercentage)
    const minimumHeight = 20

    return {
      height: `${eventHeight < minimumHeight ? minimumHeight : eventHeight > maxHeight ? maxHeight : eventHeight}px`,
      left: `${safeLeftPosition}%`,
      maxWidth: `${widthPercentage}%`,
      minWidth: `${widthPercentage}%`,
      top: `${eventTop}px`,
      zIndex: 10 + indexOnHour
    }
  }

  const handlers: Handlers = {
    handleEventStyling,
    handleAddEvent: (event: Event) => store.addEvent(event),
    handleUpdateEvent: (event: Event) => store.updateEvent(event),
    handleDeleteEvent: (id: string) => store.deleteEvent(id)
  }

  const getters: Getters = {
    getDaysInMonth: store.getDaysInMonth,
    getEventsForDay: store.getEventsForDay,
    getDaysInWeek: store.getDaysInWeek,
    getWeekNumber: store.getWeekNumber,
    getDayName: store.getDayName
  }

  return {
    events: computed(() => store.events),
    handlers,
    getters,
    weekStartsOn: computed(() => store.weekStartsOn)
  }
}
