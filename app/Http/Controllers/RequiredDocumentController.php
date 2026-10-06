<?php

namespace App\Http\Controllers;

use App\Models\RequiredDocument;
use Illuminate\Http\Request;

class RequiredDocumentController extends Controller
{
    public function update(Request $request, $id)
    {
        $reqDoc = RequiredDocument::findOrFail($id);
        
        $data = $request->validate([
            'document_name' => 'required',
            'document_type' => 'required',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);
        
        $reqDoc->update($data);
        
        return back()->with('success', 'Document updated successfully');
    }

    public function destroy($id)
    {
        $reqDoc = RequiredDocument::findOrFail($id);
        
        // Delete related document
        $reqDoc->document()->delete();
        $reqDoc->delete();
        
        return back()->with('success', 'Document deleted successfully');
    }
}
