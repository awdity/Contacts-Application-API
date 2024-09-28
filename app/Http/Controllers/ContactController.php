<?php
namespace App\Http\Controllers;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
class ContactController extends Controller
{
    // Store a new contact for the authenticated user
    public function store(Request $request)
    {
        //Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:contacts',
            'phone'    => 'required|string|max:15',
        ]);

        // If validation fails, throw a ValidationException
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->errors()],422);
        }

        try{
            $contact = Contact::create([
                'user_id' => Auth::id(),
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
            ]);
            return new ContactResource($contact);

        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    // Retrieve all contacts for the authenticated user with pagination
    public function index()
    {
        try{
            $contacts = Contact::where('user_id', Auth::id())->paginate(10);
            return ContactResource::collection($contacts);

        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    // Show a single contact by ID for the authenticated user
    public function show($id)
    {
        try{
            $contact = Contact::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
            return new ContactResource($contact);

        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    // Update an existing contact for the authenticated user
    public function update(Request $request, $id)
    {
        try{
            $contact = Contact::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
            $contact->update($request->all());
            return new ContactResource($contact);

        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    // Delete a contact by ID for the authenticated user
    public function destroy($id)
    {
        try{
            $contact = Contact::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
            $contact->delete();
            return response()->json(['message' => 'Contact deleted successfully.']);

        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    public function exportContacts()
    {
        
        // Get the contacts for the authenticated user
        $contacts = Contact::where('user_id', Auth::id())->get();

         // Check if the user has any contacts
        if ($contacts->isEmpty()) {
            return response()->json(['message' => 'No contacts found'], 404);
        }
        try{
            // Define the CSV file name
            $fileName = 'contacts.csv';

            // Use a closure to handle the streamed response
            return Response::stream(function() use ($contacts) {
                // Open the output stream
                $handle = fopen('php://output', 'w');
                // Check if the handle is valid
                if ($handle === false) {
                    throw new \Exception('Could not open output stream');
                }

            // Add header to the CSV
            fputcsv($handle, ['Name', 'Email', 'Phone']);

            // Loop through contacts and add them to the CSV
            foreach ($contacts as $contact) {
                fputcsv($handle, [$contact->name, $contact->email, $contact->phone]);
            }

            // Close the handle
            fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);


        }catch(\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }

    }

    public function import(Request $request)
    {

         //Validate the incoming request data
         $validator = Validator::make($request->all(), [
           'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        // If validation fails, throw a ValidationException
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->errors()],422);
        }

        try {
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Skip the header row
            foreach (array_slice($data, 1) as $row) {
                Contact::create([
                    'user_id' => Auth::id(),
                    'name'    => $row[0],
                    'email'   => $row[1],
                    'phone'   => $row[2],
                ]);
            }

        return response()->json(['message' => 'Contacts imported successfully.'], 200);

        }catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while importing contacts.'], 500);
        }
    }

}
