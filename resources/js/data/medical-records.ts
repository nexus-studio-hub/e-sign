import type { MedicalRecord } from '@/types/medical-record'
import { commonDiagnoses } from '@/types/diagnosis'

const patientNames = [
  'Budi Santoso', 'Siti Nurhaliza', 'Ahmad Wijaya', 'Dewi Lestari', 'Rudi Hartono',
  'Ani Kusuma', 'Joko Saputra', 'Rina Putri', 'Agus Nugroho', 'Lina Rahayu',
  'Hadi Pratama', 'Maya Lestari', 'Eko Wijaya', 'Sari Dewi', 'Doni Hartono',
  'Fitri Santoso', 'Bambang Kusuma', 'Wati Saputra', 'Yudi Pratama', 'Ratna Putri',
  'Andi Nugroho', 'Citra Rahayu', 'Dian Wijaya', 'Eka Lestari', 'Fajar Santoso',
  'Gita Kusuma', 'Hendra Saputra', 'Indah Pratama', 'Jaya Nugroho', 'Kartika Rahayu'
]

const doctors = [
  { id: 1, name: 'Dr. Andi Wijaya', clinic: 'General' },
  { id: 2, name: 'Dr. Budi Santoso', clinic: 'Internal Medicine' },
  { id: 3, name: 'Dr. Citra Dewi', clinic: 'Pediatrics' },
  { id: 4, name: 'Dr. Dian Pratama', clinic: 'General' },
  { id: 5, name: 'Dr. Eko Saputra', clinic: 'Ophthalmology' },
  { id: 6, name: 'Dr. Fitri Kusuma', clinic: 'Dentistry' },
]

const medications = [
  'Paracetamol 500mg', 'Amoxicillin 500mg', 'Omeprazole 20mg', 'Metformin 500mg',
  'Amlodipine 5mg', 'Simvastatin 20mg', 'Vitamin C 1000mg', 'Ibuprofen 400mg',
  'Cetirizine 10mg', 'Ranitidine 150mg', 'Ciprofloxacin 500mg', 'Dexamethasone 0.5mg'
]

const complaints = [
  'Fever and headache for 3 days',
  'Persistent cough and sore throat',
  'Abdominal pain and nausea',
  'High blood pressure follow-up',
  'Diabetes routine check',
  'Back pain for 1 week',
  'Red and itchy eyes',
  'Toothache and swollen gums',
  'Chest discomfort',
  'Dizziness and fatigue',
  'Skin rash and itching',
  'Difficulty breathing'
]

const generateRecordNumber = (index: number, date: Date) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const seq = String(index).padStart(3, '0')
  return `MR-${year}${month}${day}-${seq}`
}

const calculateBMI = (weight: number, height: number) => {
  const heightInMeters = height / 100
  return Math.round((weight / (heightInMeters * heightInMeters)) * 10) / 10
}

export const generateDummyMedicalRecords = (): MedicalRecord[] => {
  const records: MedicalRecord[] = []
  const today = new Date()
  
  for (let i = 0; i < 75; i++) {
    const daysAgo = Math.floor(i / 2.5)
    const visitDate = new Date(today)
    visitDate.setDate(visitDate.getDate() - daysAgo)
    
    const patientIndex = i % patientNames.length
    const doctorIndex = i % doctors.length
    const doctor = doctors[doctorIndex]
    const diagnosisIndex = i % commonDiagnoses.length
    const diagnosis = commonDiagnoses[diagnosisIndex]
    
    const weight = 50 + (i % 40)
    const height = 150 + (i % 30)
    const bmi = calculateBMI(weight, height)
    
    const age = 20 + (i % 50)
    const gender: 'L' | 'P' = i % 2 === 0 ? 'L' : 'P'
    
    let status: 'draft' | 'completed' | 'reviewed'
    if (i < 10) status = 'draft'
    else if (i < 25) status = 'completed'
    else status = 'reviewed'
    
    const prescriptionCount = 1 + (i % 3)
    const prescriptions = Array.from({ length: prescriptionCount }, (_, j) => ({
      id: j + 1,
      medication: medications[(i + j) % medications.length],
      dosage: j === 0 ? '500mg' : '1 tablet',
      frequency: j === 0 ? '3x daily' : '2x daily',
      duration: `${3 + (j % 5)} days`,
      notes: j === 0 ? 'Take after meals' : undefined
    }))
    
    const hasSecondary = i % 3 === 0
    const secondaryDiagnoses = hasSecondary ? [
      commonDiagnoses[(diagnosisIndex + 1) % commonDiagnoses.length]
    ] : undefined
    
    const allergies = i % 5 === 0 ? ['Penicillin'] : i % 7 === 0 ? ['Aspirin', 'Seafood'] : undefined
    
    records.push({
      id: i + 1,
      record_number: generateRecordNumber(i + 1, visitDate),
      patient_id: patientIndex + 1,
      patient_name: patientNames[patientIndex],
      patient_no_rm: `RM-20240918-${String(patientIndex + 1).padStart(3, '0')}`,
      patient_age: age,
      patient_gender: gender,
      visit_date: visitDate.toISOString().split('T')[0],
      visit_type: i % 4 === 0 ? 'new' : i % 4 === 1 ? 'emergency' : 'follow_up',
      
      doctor_id: doctor.id,
      doctor_name: doctor.name,
      clinic: doctor.clinic,
      
      chief_complaint: complaints[i % complaints.length],
      present_illness: `Patient reports ${complaints[i % complaints.length].toLowerCase()} with gradual onset. No previous similar episodes.`,
      past_medical_history: i % 4 === 0 ? 'Hypertension for 5 years' : undefined,
      family_history: i % 6 === 0 ? 'Father has diabetes' : undefined,
      allergies,
      
      vital_signs: {
        blood_pressure: `${110 + (i % 30)}/${70 + (i % 20)}`,
        temperature: 36 + (i % 3) + (Math.random() * 0.9),
        pulse: 60 + (i % 40),
        respiratory_rate: 16 + (i % 6),
        weight,
        height,
        bmi,
        oxygen_saturation: i % 3 === 0 ? 95 + (i % 5) : undefined
      },
      
      physical_exam: `General condition: ${i % 3 === 0 ? 'Good' : 'Fair'}. Alert and oriented. ${i % 2 === 0 ? 'No acute distress.' : 'Mild discomfort noted.'}`,
      
      primary_diagnosis: diagnosis,
      secondary_diagnoses: secondaryDiagnoses,
      
      treatment_plan: `Conservative management with medications. ${i % 3 === 0 ? 'Lifestyle modifications advised.' : 'Rest and adequate hydration recommended.'}`,
      procedures: i % 5 === 0 ? ['Blood pressure measurement', 'Physical examination'] : undefined,
      
      prescriptions,
      
      lab_results: i % 4 === 0 ? 'Blood glucose: 110 mg/dL (normal)' : undefined,
      imaging_results: i % 8 === 0 ? 'Chest X-ray: Normal' : undefined,
      
      follow_up_date: i % 3 === 0 ? new Date(visitDate.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] : undefined,
      follow_up_notes: i % 3 === 0 ? 'Follow-up in 1 week to monitor progress' : undefined,
      
      total_cost: 150000 + (i * 10000),
      payment_status: i % 3 === 0 ? 'paid' : i % 3 === 1 ? 'insurance' : 'pending',
      
      status,
      created_at: visitDate.toISOString(),
      updated_at: visitDate.toISOString(),
      reviewed_by: status === 'reviewed' ? doctor.name : undefined,
      reviewed_at: status === 'reviewed' ? visitDate.toISOString() : undefined
    })
  }
  
  return records.sort((a, b) => new Date(b.visit_date).getTime() - new Date(a.visit_date).getTime())
}

export const dummyMedicalRecords = generateDummyMedicalRecords()
