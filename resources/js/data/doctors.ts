import type { Doctor, DoctorFormData } from '@/types/doctor'

const STORAGE_KEY = 'doctors'

const defaultDoctors: Doctor[] = [
  {
    id: '1',
    name: 'Dr. Andi Wijaya',
    specialization_id: '1',
    photo: 'https://i.pravatar.cc/150?img=12',
    email: 'andi.wijaya@clinic.com',
    phone: '+62 812-3456-7890',
    license_number: 'SIP/001/2020',
    gender: 'male',
    date_of_birth: '1985-05-15',
    address: 'Jl. Sudirman No. 123',
    city: 'Jakarta',
    education: 'S1 Kedokteran Universitas Indonesia',
    experience_years: 10,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '2',
    name: 'Dr. Budi Santoso',
    specialization_id: '2',
    photo: 'https://i.pravatar.cc/150?img=13',
    email: 'budi.santoso@clinic.com',
    phone: '+62 813-4567-8901',
    license_number: 'SIP/002/2020',
    gender: 'male',
    date_of_birth: '1982-08-20',
    address: 'Jl. Thamrin No. 456',
    city: 'Jakarta',
    education: 'S1 Kedokteran Gigi Universitas Padjadjaran',
    experience_years: 12,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '3',
    name: 'Dr. Citra Dewi',
    specialization_id: '3',
    photo: 'https://i.pravatar.cc/150?img=47',
    email: 'citra.dewi@clinic.com',
    phone: '+62 814-5678-9012',
    license_number: 'SIP/003/2020',
    gender: 'female',
    date_of_birth: '1990-03-10',
    address: 'Jl. Gatot Subroto No. 789',
    city: 'Jakarta',
    education: 'S1 Kedokteran Universitas Gadjah Mada',
    experience_years: 7,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '4',
    name: 'Dr. Dian Pratama',
    specialization_id: '1',
    photo: 'https://i.pravatar.cc/150?img=33',
    email: 'dian.pratama@clinic.com',
    phone: '+62 815-6789-0123',
    license_number: 'SIP/004/2020',
    gender: 'male',
    date_of_birth: '1988-11-25',
    address: 'Jl. Kuningan No. 321',
    city: 'Jakarta',
    education: 'S1 Kedokteran Universitas Airlangga',
    experience_years: 9,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '5',
    name: 'Dr. Eko Saputra',
    specialization_id: '4',
    photo: 'https://i.pravatar.cc/150?img=14',
    email: 'eko.saputra@clinic.com',
    phone: '+62 816-7890-1234',
    license_number: 'SIP/005/2020',
    gender: 'male',
    date_of_birth: '1987-07-18',
    address: 'Jl. Rasuna Said No. 654',
    city: 'Jakarta',
    education: 'S1 Kedokteran Universitas Diponegoro',
    experience_years: 8,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  },
  {
    id: '6',
    name: 'Dr. Fitri Kusuma',
    specialization_id: '2',
    photo: 'https://i.pravatar.cc/150?img=48',
    email: 'fitri.kusuma@clinic.com',
    phone: '+62 817-8901-2345',
    license_number: 'SIP/006/2020',
    gender: 'female',
    date_of_birth: '1992-01-30',
    address: 'Jl. HR Rasuna Said No. 987',
    city: 'Jakarta',
    education: 'S1 Kedokteran Gigi Universitas Trisakti',
    experience_years: 6,
    is_active: true,
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00'
  }
]

const loadDoctors = (): Doctor[] => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (stored) {
      return JSON.parse(stored)
    }
  } catch (e) {
    console.error('Failed to load doctors from localStorage', e)
  }
  return defaultDoctors
}

const saveDoctors = (doctors: Doctor[]) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(doctors))
  } catch (e) {
    console.error('Failed to save doctors to localStorage', e)
  }
}

let allDoctors = loadDoctors()

export const getAllDoctors = (): Doctor[] => {
  return allDoctors
}

export const getDoctorById = (id: string): Doctor | undefined => {
  return allDoctors.find(d => d.id === id)
}

export const getDoctorsBySpecialization = (specializationId: string): Doctor[] => {
  return allDoctors.filter(d => d.specialization_id === specializationId)
}

export const getActiveDoctors = (): Doctor[] => {
  return allDoctors.filter(d => d.is_active)
}

export const createDoctor = (data: DoctorFormData): Doctor => {
  const newId = String(Math.max(...allDoctors.map(d => parseInt(d.id)), 0) + 1)
  const now = new Date().toISOString()
  
  const newDoctor: Doctor = {
    id: newId,
    ...data,
    created_at: now,
    updated_at: now
  }
  
  allDoctors.push(newDoctor)
  saveDoctors(allDoctors)
  
  return newDoctor
}

export const updateDoctor = (id: string, data: DoctorFormData): void => {
  const index = allDoctors.findIndex(d => d.id === id)
  if (index !== -1) {
    allDoctors[index] = {
      ...allDoctors[index],
      ...data,
      updated_at: new Date().toISOString()
    }
    saveDoctors(allDoctors)
  }
}

export const deleteDoctor = (id: string): void => {
  allDoctors = allDoctors.filter(d => d.id !== id)
  saveDoctors(allDoctors)
}

export const canDeleteDoctor = (id: string): boolean => {
  // Check if doctor is assigned to any schedules
  // Will be checked in the UI before deletion
  return true
}
