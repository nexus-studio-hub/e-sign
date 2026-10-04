import type { Receptionist, ReceptionistFormData } from '@/types/receptionist'

const STORAGE_KEY = 'receptionists'

const defaultReceptionists: Receptionist[] = [
  {
    id: '1',
    name: 'Siti Nurhaliza',
    photo: 'https://i.pravatar.cc/150?img=45',
    email: 'siti.nurhaliza@clinic.com',
    phone: '+62 821-1111-2222',
    gender: 'female',
    date_of_birth: '1995-06-12',
    address: 'Jl. Merdeka No. 100',
    city: 'Jakarta',
    shift: 'morning',
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '2',
    name: 'Rina Wati',
    photo: 'https://i.pravatar.cc/150?img=48',
    email: 'rina.wati@clinic.com',
    phone: '+62 821-2222-3333',
    gender: 'female',
    date_of_birth: '1992-09-20',
    address: 'Jl. Pahlawan No. 200',
    city: 'Jakarta',
    shift: 'afternoon',
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '3',
    name: 'Dewi Lestari',
    photo: 'https://i.pravatar.cc/150?img=49',
    email: 'dewi.lestari@clinic.com',
    phone: '+62 821-3333-4444',
    gender: 'female',
    date_of_birth: '1998-03-15',
    address: 'Jl. Diponegoro No. 300',
    city: 'Jakarta',
    shift: 'morning',
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '4',
    name: 'Ahmad Fauzi',
    photo: 'https://i.pravatar.cc/150?img=15',
    email: 'ahmad.fauzi@clinic.com',
    phone: '+62 821-4444-5555',
    gender: 'male',
    date_of_birth: '1994-11-08',
    address: 'Jl. Veteran No. 400',
    city: 'Jakarta',
    shift: 'afternoon',
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  }
]

export const getAllReceptionists = (): Receptionist[] => {
  const stored = localStorage.getItem(STORAGE_KEY)
  if (!stored) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(defaultReceptionists))
    return defaultReceptionists
  }
  return JSON.parse(stored)
}

export const getReceptionistById = (id: string): Receptionist | undefined => {
  const receptionists = getAllReceptionists()
  return receptionists.find(r => r.id === id)
}

export const createReceptionist = (data: ReceptionistFormData): Receptionist => {
  const receptionists = getAllReceptionists()
  const newReceptionist: Receptionist = {
    ...data,
    id: Date.now().toString(),
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }
  receptionists.push(newReceptionist)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(receptionists))
  return newReceptionist
}

export const updateReceptionist = (id: string, data: ReceptionistFormData): Receptionist | null => {
  const receptionists = getAllReceptionists()
  const index = receptionists.findIndex(r => r.id === id)
  if (index === -1) return null
  
  receptionists[index] = {
    ...receptionists[index],
    ...data,
    updated_at: new Date().toISOString()
  }
  localStorage.setItem(STORAGE_KEY, JSON.stringify(receptionists))
  return receptionists[index]
}

export const deleteReceptionist = (id: string): boolean => {
  const receptionists = getAllReceptionists()
  const filtered = receptionists.filter(r => r.id !== id)
  if (filtered.length === receptionists.length) return false
  
  localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered))
  return true
}
