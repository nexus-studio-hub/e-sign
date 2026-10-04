import type { Specialization, SpecializationFormData } from '@/types/specialization'

const STORAGE_KEY = 'specializations'

const defaultSpecializations: Specialization[] = [
  {
    id: '1',
    name: 'General Practice',
    name_id: 'Umum',
    code: 'GEN',
    color: '#3B82F6',
    description: 'General medical practice',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '2',
    name: 'Dentistry',
    name_id: 'Gigi',
    code: 'DENT',
    color: '#10B981',
    description: 'Dental care and oral health',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '3',
    name: 'Pediatrics',
    name_id: 'Anak',
    code: 'PED',
    color: '#F59E0B',
    description: 'Child healthcare',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '4',
    name: 'Ophthalmology',
    name_id: 'Mata',
    code: 'OPH',
    color: '#8B5CF6',
    description: 'Eye care and vision',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '5',
    name: 'Cardiology',
    name_id: 'Jantung',
    code: 'CARD',
    color: '#EF4444',
    description: 'Heart and cardiovascular care',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '6',
    name: 'Dermatology',
    name_id: 'Kulit',
    code: 'DERM',
    color: '#EC4899',
    description: 'Skin care and treatment',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  }
]

const loadSpecializations = (): Specialization[] => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (stored) {
      return JSON.parse(stored)
    }
  } catch (e) {
    console.error('Failed to load specializations from localStorage', e)
  }
  return defaultSpecializations
}

const saveSpecializations = (specializations: Specialization[]) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(specializations))
  } catch (e) {
    console.error('Failed to save specializations to localStorage', e)
  }
}

let allSpecializations = loadSpecializations()

export const getAllSpecializations = (): Specialization[] => {
  return allSpecializations
}

export const getSpecializationById = (id: string): Specialization | undefined => {
  return allSpecializations.find(s => s.id === id)
}

export const getSpecializationByCode = (code: string): Specialization | undefined => {
  return allSpecializations.find(s => s.code === code)
}

export const createSpecialization = (data: SpecializationFormData): Specialization => {
  const newId = String(Math.max(...allSpecializations.map(s => parseInt(s.id)), 0) + 1)
  const now = new Date().toISOString()
  
  const newSpecialization: Specialization = {
    id: newId,
    ...data,
    created_at: now,
    updated_at: now
  }
  
  allSpecializations.push(newSpecialization)
  saveSpecializations(allSpecializations)
  
  return newSpecialization
}

export const updateSpecialization = (id: string, data: SpecializationFormData): void => {
  const index = allSpecializations.findIndex(s => s.id === id)
  if (index !== -1) {
    allSpecializations[index] = {
      ...allSpecializations[index],
      ...data,
      updated_at: new Date().toISOString()
    }
    saveSpecializations(allSpecializations)
  }
}

export const deleteSpecialization = (id: string): void => {
  allSpecializations = allSpecializations.filter(s => s.id !== id)
  saveSpecializations(allSpecializations)
}

export const canDeleteSpecialization = (id: string): boolean => {
  // Check if any doctors use this specialization
  // Will be implemented after doctors.ts is created
  return true
}
