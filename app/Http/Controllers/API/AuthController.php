<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Models\WhyEwent;
use App\Models\UserKycLog;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Offer;
use App\Models\RentalPrice;
use App\Models\Order;
use App\Models\AsignedVehicle;
use App\Models\Policy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    //  private function storeFileWithCustomName($file, $directory)
    // {
    //     // Generate a custom filename: random 4 digits + timestamp + file extension
    //     $filename = rand(1000, 9999) . '_' . time() . '.' . $file->getClientOriginalExtension();

    //     // Store the file in the specified directory and return its path
    //     return $file->storeAs($directory, $filename, 'public');
    // }
    public function register(Request $request)
    {
       // Custom validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|digits:10|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            // 'driving_licence' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // 'govt_id_card' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // 'cancelled_cheque' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // 'current_address_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first()
            ], 422);
        }
        DB::beginTransaction();

        try {
            // Initialize paths to null
            // $drivinglicencePath = null;
            // $govtIdCardPath = null;
            // $cancelledChequePath = null;
            // $currentAddressProofPath = null;

            // // Handle file uploads with custom naming if files are provided
            // if ($request->hasFile('driving_licence')) {
            //     $drivinglicencePath = storeFileWithCustomName($request->file('driving_licence'), 'uploads/driving_licences');
            // }
            
            // if ($request->hasFile('govt_id_card')) {
            //     $govtIdCardPath = storeFileWithCustomName($request->file('govt_id_card'), 'uploads/govt_id_cards');
            // }
            
            // if ($request->hasFile('cancelled_cheque')) {
            //     $cancelledChequePath = storeFileWithCustomName($request->file('cancelled_cheque'), 'uploads/cancelled_cheques');
            // }
            
            // if ($request->hasFile('current_address_proof')) {
            //     $currentAddressProofPath = storeFileWithCustomName($request->file('current_address_proof'), 'uploads/address_proofs');
            // }
            // Create the user
            $user = User::create([
                'name' => ucwords($request->name),
                'customer_id' => MakingCustomerId(),
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                // 'driving_licence' => $drivinglicencePath,
                // 'govt_id_card' => $govtIdCardPath,
                // 'cancelled_cheque' => $cancelledChequePath,
                // 'current_address_proof' => $currentAddressProofPath,
            ]);

            // / Commit the transaction
            DB::commit();
            // Return a response with the created user data
            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();

            // Return error response
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle user login and issue token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {
     
        // Validate the login request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }
     
        // Determine if username is an email or mobile number
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
     
        // Additional check for mobile format
        if ($loginField === 'mobile' && !preg_match('/^\d{10}$/', $request->username)) {
            return response()->json([
                'message' => 'Invalid mobile number. It must be a 10-digit number.',
            ], 422);
        }
       
        // Attempt login
        if (Auth::attempt([$loginField => $request->username, 'password' => $request->password])) {
            $user = Auth::guard('sanctum')->user();
            // dd($user);
            // Use user's name as the token name
            $tokenName = str_replace(' ', '_', $user->name) . '_' . $user->id . '_token';

            // Delete any existing tokens with the same name before generating a new one
            $user->tokens()->where('tokenable_id', $user->id)->delete();
            // dd($tokenName);
            // Generate new token using Laravel Sanctum
            $token = $user->createToken($tokenName)->plainTextToken;
            $user->is_verified = CheckUserStatus($user->id);
            $user->rating = AdminRatings($user->id);
            // Return response with token
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        }
        // If login fails, return an error response
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], 422);
    }

    // Step 1: Request OTP
    public function requestotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10|exists:users,mobile',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $mobile = $request->mobile;

        // Generate OTP
        // $otp = rand(100000, 999999);
        $otp = 1234;

        // Store OTP in a database or cache (e.g., Redis)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['mobile' => $mobile],
            ['otp' => $otp, 'created_at' => now()]
        );

        // Send OTP to mobile (using a hypothetical sendSms function)
        // $this->sendSms($mobile, "Your OTP is: $otp");

        return response()->json([
            'message' => 'OTP sent to your mobile number.',
        ], 200);
    }

     // Step 2: Verify OTP
     public function verifyOtp(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'mobile' => 'required|digits:10|exists:users,mobile',
             'otp' => 'required|digits:4',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                'status' => false,
                 'message' => $validator->errors()->first(),
                 'errors' => $validator->errors()->first(),
             ], 422);
         }
 
         $mobile = $request->mobile;
         $otp = $request->otp;
 
         // Check OTP in the database
         $record = DB::table('password_reset_tokens')
             ->where('mobile', $mobile)
             ->where('otp', $otp)
             ->first();
             
         if (!$record || now()->diffInSeconds($record->created_at) > 60) {
             return response()->json([
                 'message' => 'Invalid or expired OTP.',
             ], 422);
         }
 
         // Mark OTP as verified (optional, cleanup can be added)
         return response()->json([
             'message' => 'OTP verified successfully.',
         ], 200);
     }

     // Step 3: Reset Password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10|exists:users,mobile',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $mobile = $request->mobile;
        $password = $request->password;

        // Update user's password
        $user = User::where('mobile', $mobile)->first();
        $user->password = Hash::make($password);
        $user->save();

        // Cleanup OTP record
        DB::table('password_reset_tokens')->where('mobile', $mobile)->delete();

        return response()->json([
            'message' => 'Password reset successfully.',
        ], 200);
    }

    protected function getAuthenticatedUser()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        return $user;
    }
    public function userProfile()
    {
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        
        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404); // 404 Not Found
        }
    
        // Return the user profile data
        return response()->json([
            'data' => $user,
            'message' => 'User profile retrieved successfully.',
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id', // Ensure the ID exists in the users table
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        // dd($validator);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first()
            ], 422);
        }
        // Find the user by ID
        $user = User::find($request->user);
    
        // If the user is not found, return a response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404); // 404 Not Found
        }
    
        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 400); // 400 Bad Request
        }
    
        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }
    public function fetchBanners()
    {
        // Fetch active banners (status = 1)
        $banners = Banner::where('status', 1)->orderBy('id', 'desc')->get();

        // Check if any banners are found
        if ($banners->isEmpty()) {
            // Return response if no active banners are found
            return response()->json([
                'status' => false,
                'message' => 'No active banners available.',
                'data' => [],
            ], 404); // 404 Not Found
        }

        // Return response with banners data
        return response()->json([
            'status' => true,
            'message' => 'Banners fetched successfully.',
            'data' => $banners,
        ], 200); // 200 OK
    }

    public function fetchFaqs()
    {
        $faqs = Faq::orderBy('question', 'ASC')->get();
    
        if ($faqs->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No faqs available.',
                'data' => [],
            ], 404); 
        }

        return response()->json([
            'status' => true,
            'message' => 'Faqs fetched successfully.',
            'data' => $faqs,
        ], 200); 
    }

    // profile update
    public function updateProfile(Request $request){
         // Validate the request data
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id', // Ensure the ID exists in the users table
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'email' => [
                'required',
                'email',
                // Check that the email is unique except for the given user_id
                Rule::unique('users', 'email')->ignore($request->id, 'id'),
            ],
            'mobile' => [
                'required',
                'digits:10',
                // Check that the mobile is unique except for the given user_id
                Rule::unique('users', 'mobile')->ignore($request->id, 'id'),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('id', $request->id)->first();
        $user->name = ucwords($request->name);
        $user->address = $request->address;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
       // Handle image upload (if provided)
        if ($request->hasFile('image')) {
            $image = storeFileWithCustomName($request->file('image'), 'uploads/user');
            $user->profile_image = $image;
        }
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully!',
            'data' => $user,
        ], 200); 
    }
    public function updateDocument(Request $request){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        $validator = Validator::make($request->all(), [
            'driving_licence' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'driving_licence_back' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'govt_id_card' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'govt_id_card_back' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'cancelled_cheque' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'cancelled_cheque_back' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'current_address_proof' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'current_address_proof_back' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
        ]);

        // Check if validation fails
        // dd($validator);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('id', $user->id)->first();
        
       // Handle image upload (if provided)
        if ($request->hasFile('driving_licence') || $request->hasFile('driving_licence_back')) {

            if($request->hasFile('driving_licence')){
                $driving_licence = storeFileWithCustomName($request->file('driving_licence'), 'uploads/driving_licences');
                $user->driving_licence = $driving_licence;
            }
            
            if($request->hasFile('driving_licence_back')){
                $driving_licence_back = storeFileWithCustomName($request->file('driving_licence_back'), 'uploads/driving_licences');
                $user->driving_licence_back = $driving_licence_back;
            }

            $user->driving_licence_status = 1;
          
            $existing_data = UserKycLog::where('user_id', $user->id)->where('document_type','Driving Licence')->where('status', 'Uploaded')->first();
            $store = new UserKycLog;
            if($existing_data){
                $store->status = 'Re-uploaded';
            }else{
                $store->status = "Uploaded";
            }
            $store->user_id = $user->id;
            $store->created_at = date('Y-m-d h:i:s');
            $store->document_type = 'Driving Licence';
            $store->save();
            
        }
        if ($request->hasFile('govt_id_card') || $request->hasFile('govt_id_card_back')) {
            if($request->hasFile('govt_id_card')){
                $govt_id_card = storeFileWithCustomName($request->file('govt_id_card'), 'uploads/govt_id_cards');
                $user->govt_id_card = $govt_id_card;
            }

            if($request->hasFile('govt_id_card_back')){
                $govt_id_card_back = storeFileWithCustomName($request->file('govt_id_card_back'), 'uploads/govt_id_cards');
                $user->govt_id_card_back = $govt_id_card_back;
            }
            
            $user->govt_id_card_status = 1;

            $existing_data = UserKycLog::where('user_id', $user->id)->where('document_type','Govt ID Card')->where('status', 'Uploaded')->first();
            $store = new UserKycLog;
            if($existing_data){
                $store->status = 'Re-uploaded';
            }else{
                $store->status = "Uploaded";
            }
            $store->user_id = $user->id;
            $store->created_at = date('Y-m-d h:i:s');
            $store->document_type = 'Govt ID Card';
            $store->save();

        }
        if ($request->hasFile('cancelled_cheque') || $request->hasFile('cancelled_cheque_back')) {
            if($request->hasFile('cancelled_cheque')){
                $cancelled_cheque = storeFileWithCustomName($request->file('cancelled_cheque'), 'uploads/cancelled_cheques');
                $user->cancelled_cheque = $cancelled_cheque;
            }

            if($request->hasFile('cancelled_cheque_back')){
                $cancelled_cheque_back = storeFileWithCustomName($request->file('cancelled_cheque_back'), 'uploads/cancelled_cheques');
                $user->cancelled_cheque_back = $cancelled_cheque_back;
            }
            
            $user->cancelled_cheque_status = 1;

            $existing_data = UserKycLog::where('user_id', $user->id)->where('document_type','Cancelled Cheque')->where('status', 'Uploaded')->first();
            $store = new UserKycLog;
            if($existing_data){
                $store->status = 'Re-uploaded';
            }else{
                $store->status = "Uploaded";
            }
            $store->user_id = $user->id;
            $store->created_at = date('Y-m-d h:i:s');
            $store->document_type = 'Cancelled Cheque';
            $store->save();
        }
        if ($request->hasFile('current_address_proof') || $request->hasFile('current_address_proof_back')) {

            if($request->hasFile('current_address_proof')){
                $current_address_proof = storeFileWithCustomName($request->file('current_address_proof'), 'uploads/address_proofs');
                $user->current_address_proof = $current_address_proof;
            }

            if($request->hasFile('current_address_proof_back')){
                $current_address_proof_back = storeFileWithCustomName($request->file('current_address_proof_back'), 'uploads/address_proofs');
                $user->current_address_proof_back = $current_address_proof_back;
            }
            

            $user->current_address_proof_status = 1;

            $existing_data = UserKycLog::where('user_id', $user->id)->where('document_type','Current Address Proof')->where('status', 'Uploaded')->first();
            $store = new UserKycLog;
            if($existing_data){
                $store->status = 'Re-uploaded';
            }else{
                $store->status = "Uploaded";
            }
            $store->user_id = $user->id;
            $store->created_at = date('Y-m-d h:i:s');
            $store->document_type = 'Current Address Proof';
            $store->save();
           
        }
        $user->status = 1;
        // $user->is_verified = 'unverified';
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile document updated successfully!',
            'data' => $user,
        ], 200); 
    }
    
    public function revokeTokens()
    {
        try {

            $user = $this->getAuthenticatedUser();
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user; // Return the response if the user is not authenticated
            }
            // Delete tokens where tokenable_id matches the user ID
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->delete();
            return response()->json([
                'status' => true,
                'message' => 'All tokens for the user have been removed successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove tokens.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function ProductList(Request $request)
    {
        // Capture the search term from the request
        $search = $request->input('search'); // Assuming 'search' is passed as a query parameter

        // Retrieve products based on the search criteria
        $products = Product::query()
        ->select(
            'id',
            'title',
            'position',
            'types',
            'short_desc',
            'is_driving_licence_required',
            'image',
            'status'
        )
        ->when($search, function ($query) use ($search) {
            // Group search conditions with OR logic
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('types', 'like', '%' . $search . '%');
            });
        })
        ->with([
            'rentalprice' => function ($query) {
                $query->select('id', 'product_id', 'duration','subscription_type', 'deposit_amount', 'rental_amount'); // Select only necessary columns
            },
            'category:id,title',      // Load specific columns for 'category'
            'subCategory:id,title',   // Load specific columns for 'subcategory'
            'features:id,product_id,title'       // Load specific columns for 'features'
        ])
        ->where('status', 1) // Filter active products
        ->orderBy('position', 'ASC') // First order by position
        ->orderBy('title', 'ASC') // Then order by title
        ->get();

        // Process each product to set rental price details
        foreach ($products as $product) {
            $rental = $product->rentalprice->first();
            $product->subscription_type = $rental ? $rental->subscription_type : 0;
            $product->deposit_amount = $rental ? $rental->deposit_amount : 0;
            $product->rental_duration = $rental ? $rental->duration : 0;
            $product->rental_amount = $rental ? $rental->rental_amount : 0;
        }
        // Return the product list as a JSON response
        return response()->json([
            'status' => true,
            'data' => $products,
            'message' => 'Getting product list.',
        ], 200);
    }
    
    public function ProductDetails($id)
    {
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }

        // Retrieve the product by ID and ensure it's active (status = 1)
        $data = Product::where('id', $id)
            ->where('status', 1)
            ->with([
                'rentalprice' => function ($query) {
                    $query->select('id', 'product_id', 'duration', 'subscription_type','deposit_amount', 'rental_amount');
                },
                'ProductImages:product_id,image', // Eager load product images
                'category:id,title',             // Eager load category with specific columns
                'subCategory:id,title',           // Eager load sub-category with specific columns
                'features:id,product_id,title'           // Eager load sub-category with specific columns
            ])
            ->first();

 
        // Check if product exists
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        // Combine all images into a single array
        $allImages = collect($data->ProductImages)
            ->pluck('image') // Get all images from the relationship
            ->prepend($data->image) // Add the main image at the beginning
            ->unique() // Ensure no duplicate images
            ->toArray(); // Convert to array

        $allfeatures = collect($data->features)
            ->pluck('title') // Get all images from the relationship
            ->unique() // Ensure no duplicate images
            ->toArray(); 
        $product_features = [];
        if(count($allfeatures)>0){
            foreach($allfeatures as $k=>$fitem){
                $product_features[] =$fitem;
            }
        }
        $related_products = Product::select(
            'id',
            'title',
            'position',
            'types',
            'short_desc',
            'image',
            'category_id',
            'sub_category_id',
            'status'
        )
        ->where('id', '!=', $data->id) // Exclude the current product
        ->where('status', 1) // Ensure the product is active
        ->where(function ($query) use ($data) {
            $query->where('category_id', $data->category_id) // Match the same category
                    ->orWhere('sub_category_id', $data->sub_category_id); // Or match the same sub-category
        })
        ->orderBy('position', 'ASC') // Order by position first
        ->orderBy('title', 'ASC') // Then order by title
        ->limit(10) // Limit to 10 results
        ->get();
            
        // Prepare product details object
        $product_data = (object) [];
        // $product_data->stock_qty = $data->stock_qty;
        $product_data->id = $data->id;
        $product_data->title = $data->title;
        $product_data->types = $data->types;
        $product_data->short_desc = $data->short_desc;
        // $product_data->long_desc = $data->long_desc;
        $product_data->category = $data->category ? $data->category->title : null;
        $product_data->sub_category = $data->subCategory ? $data->subCategory->title : null;
        // $product_data->is_selling = $data->is_selling;
        // $product_data->base_price = $data->base_price;
        // $product_data->display_price = $data->display_price;
        // $product_data->is_rent = $data->is_rent;
        // $product_data->rent_duration = $data->rent_duration;
        // $product_data->per_rent_price = $data->per_rent_price;
        $product_data->status = $data->status;
        $product_data->all_features = $product_features;
        $product_data->all_images = $allImages; // Set combined images array
        $product_data->rental_price = $data->rentalprice;
        $product_data->related_products = $related_products;
        $product_data->is_driving_licence_required = $data->is_driving_licence_required;
        $product_data->customer_reviews = ProductReviews($data->id);
        // Return the product details as a JSON response
        $features = $allfeatures;
        return response()->json([
            'status' => true,
            'data' => $product_data,
            'message' => 'Getting product details.'
        ], 200);
    }

    public function ProductFilter(Request $request)
    {
        $search = $request->input('filter');
        // If no filter value is provided, return empty array
        if (!$search) {
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'No search value provided.',
            ], 200);
        }
    
        // Proceed with filtering if search term exists
        $products = Product::query()
            ->select(
                'id',
                'title',
                'position',
                'types',
                'short_desc',
                'is_driving_licence_required',
                'image',
                'status'
            )
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('types', 'like', '%' . $search . '%');
            })
            ->orderBy('position', 'ASC')
            ->orderBy('title', 'ASC')
            ->get();
    
        return response()->json([
            'status' => true,
            'data' => $products,
            'message' => count($products) > 0 ? 'Getting product list.' : 'Data not found!',
        ], 200);
    }
    
    public function HomePage()
    {
        // Fetching the banners
        $banners = Banner::where('status', 1)->orderBy('id', 'desc')->get();
        $why_ewent = WhyEwent::where('status', 1)->orderBy('id', 'desc')->get();
        
        // Fetching the FAQs
        $faqs = Faq::orderBy('question', 'ASC')->get();
        
        // Fetching the products with eager loading
        $products = Product::select('id', 'title', 'position', 'types', 'short_desc', 'image', 'status', 'is_driving_licence_required')->where('status', 1)
            ->with([
                'rentalprice' => function ($query) {
                    $query->select('id', 'product_id', 'duration', 'subscription_type', 'deposit_amount', 'rental_amount'); // Select only necessary columns
                }     // Load specific columns for 'features'
            ])
            ->orderBy('position', 'ASC')
            ->orderBy('title', 'ASC') // Order products by title
            ->limit(10)
            ->get();
            foreach ($products as $product) {
                $rental = $product->rentalprice->first();
                $product->subscription_type = $rental ? $rental->subscription_type : 0;
                $product->deposit_amount = $rental ? $rental->deposit_amount : 0;
                $product->rental_duration = $rental ? $rental->duration : 0;
                $product->rental_amount = $rental ? $rental->rental_amount : 0;
            }
        // Check if there are any banners, FAQs, or products
        if ($banners->isEmpty() && $faqs->isEmpty() && $products->isEmpty()) {
            // Return a response if no data is found
            return response()->json([
                'status' => false,
                'message' => 'No data found',
            ], 404);
        }
    
        // Prepare a single array for the API response
        $response = [
            'why_ewent' => $why_ewent,
            'banners' => $banners,
            'faqs' => $faqs,
            'products' => $products
        ];
    
        // Return the data if found
        return response()->json([
            'status' => true,
            'response' => $response,
            'message' => 'Home page data',
        ], 200);
    }

    public function DocumentStatus(){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        $documents= [];
       
        $data = User::select('id', 'driving_licence as driving_licence_front', 'driving_licence_back','driving_licence_status', 'govt_id_card as govt_id_card_front', 'govt_id_card_back', 'govt_id_card_status', 'cancelled_cheque as cancelled_cheque_front', 'cancelled_cheque_back','cancelled_cheque_status', 'current_address_proof as current_address_front','current_address_proof_back', 'current_address_proof_status')->where('id',$user->id)->first();
         // Check if product exists
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $documents['Driving Licence'] = [
            'front' =>$data->driving_licence_front,
            'back'=>$data->driving_licence_back,
            'status' =>$data->driving_licence_status,
        ];

        $documents['Govt ID Card'] = [
            'front' =>$data->govt_id_card_front,
            'back'=>$data->govt_id_card_back,
            'status' =>$data->govt_id_card_status,
        ];

        $documents['Cancelled Cheque'] = [
            'front' =>$data->cancelled_cheque_front,
            'back'=>$data->cancelled_cheque_back,
            'status' =>$data->cancelled_cheque_status,
        ];

        $documents['Current Address Proof'] = [
            'front' =>$data->current_address_front,
            'back'=>$data->current_address_proof_back,
            'status' =>$data->current_address_proof_status,
        ];


        $documents['Driving Licence']['history'] = UserKycLog::where('user_id', $user->id)->where('document_type', 'Driving Licence')->orderBy('id', 'ASC')->get()->map(function ($item) {
            $item->date = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i A'); // Format Date
            return $item;
        })->toArray();

        $documents['Govt ID Card']['history'] = UserKycLog::where('user_id', $user->id)->where('document_type', 'Govt ID Card')->orderBy('id', 'ASC')->get()->map(function ($item) {
            $item->date = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i A'); // Format Date
            return $item;
        })->toArray();

        $documents['Cancelled Cheque']['history'] = UserKycLog::where('user_id', $user->id)->where('document_type', 'Cancelled Cheque')->orderBy('id', 'ASC')->get()->map(function ($item) {
            $item->date = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i A'); // Format Date
            return $item;
        })->toArray();

        $documents['Current Address Proof']['history'] = UserKycLog::where('user_id', $user->id)->where('document_type', 'Current Address Proof')->orderBy('id', 'ASC')->get()->map(function ($item) {
            $item->date = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i A'); // Format Date
            return $item;
        })->toArray();

        // dd($documents);
        // Return the data if found
        return response()->json([
            'status' => true,
            'response' => $documents,
            'message' => 'User Document Status',
        ], 200);
    }

    public function OfferList(){
        $data = Offer::where('status', 'active')->orderBy('coupon_code', 'ASC')->get();
        if(count($data)==0){
            return response()->json(['status'=>false, 'message'=>'offer not found!'], 404);
        }
        return response()->json(['status'=>true, 'response'=>$data, 'message'=>'Offer Listing'], 200);
    }

    public function OrderHistory(){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        $data = Order::with('product','vehicle','exchange_vehicle')->where('user_id', $user->id)->orderBy('id', 'DESC')->get();
        if(count($data)==0){
            return response()->json(['status'=>false, 'message'=>'oder not found!'], 404);
        }
        $result = [];
        foreach($data as $key=>$item){
            $histories = $item->exchange_vehicle()->orderBy('id', 'ASC')->get();
          
            $result[$key]=[
                'order_number' => $item->order_number,
                'model' => $item->product ? $item->product->title : "N/A",
                'subscription_type' => $item->subscription ? ucwords($item->subscription->subscription_type) : "N/A",
                'deposit_amount'=>$item->deposit_amount,
                'rental_amount'=>$item->rental_amount,
                'payment_status'=>ucwords($item->payment_status),
                'rent_duration'=>$item->rent_duration.' Days',
                'status'=>$item->rent_status,
                'order_date'=>date('d-m-Y h:i A', strtotime($item->created_at)),
            ];
            foreach($histories as $index=>$history){
                $result[$key]['history'][$index]=[
                    'vehicle'=>$history->stock?$history->stock->vehicle_number:"N/A",
                    'start_date'=>date('d-m-Y h:i A', strtotime($history->start_date)),
                    'end_date'=>date('d-m-Y h:i A', strtotime($history->end_date)),
                    'status'=>ucwords($history->status),
                    'date'=>date('d-m-Y h:i A', strtotime($history->exchanged_at)),
                ];
            }
            $last_index = count($histories)+1;
            if(isset($item->vehicle)){
                $last_item = $item->vehicle;
                $result[$key]['history'][$last_index]=[
                    'vehicle'=>$last_item->stock?$last_item->stock->vehicle_number:"N/A",
                    'start_date'=>date('d-m-Y h:i A', strtotime($last_item->start_date)),
                    'end_date'=>date('d-m-Y h:i A', strtotime($last_item->end_date)),
                    'status'=>ucwords($last_item->status),
                    'date'=>date('d-m-Y h:i A', strtotime($last_item->assigned_at)),
                ];
            }
            
        }

        return response()->json(['status'=>true, 'response'=>$result, 'message'=>'Order Listing'],200);
    }

    public function paymentHistory(){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }

        $data = Order::with('product','subscription','payments')->where('user_id', $user->id)->orderBy('id', 'DESC')->get();
        $result = [];

        foreach($data as $key => $item) {
           
            foreach($item->payments as $index=>$sub_item){
                $result[$index] = [
                    'order_number' => $item->order_number,
                    'model' => $item->product ? $item->product->title : "N/A",
                    'subscription_type' => $item->subscription ? ucwords($item->subscription->subscription_type) : "N/A",
                    'payment_for' => ucwords(str_replace('_', ' ', $sub_item->order_type)),
                    'amount'=>$sub_item->amount,
                    'transaction_id'=>$sub_item->transaction_id,
                    'payment_method'=>ucwords($sub_item->payment_method),
                    'payment_date'=>date('d-m-Y h:i A', strtotime($sub_item->payment_date)),
                ];
            }
        }
        return response()->json([
            'status' => true,
            'order' => $result,
        ], 200);
      
    }
    public function SellOrderHistory($user_id){
        $data = Order::where('user_id', $user_id)->where('order_type', 'Sell')->orderBy('id', 'DESC')->get();
        if(count($data)==0){
            return response()->json(['status'=>false, 'message'=>'Order not found!'], 404);
        }

        return response()->json(['status'=>true, 'response'=>$data, 'message'=>'Order listing'],200);
    }
    public function RentOrderHistory($user_id){
        $data = Order::where('user_id', $user_id)->where('order_type', 'Rent')->orderBy('id', 'DESC')->get();
        if(count($data)==0){
            return response()->json(['status'=>false, 'message'=>'Order not found!'], 404);
        }

        return response()->json(['status'=>true, 'response'=>$data, 'message'=>'Order listing'],200);
    }

    public function OrderDetails($order_id){
        $order = Order::where('id', $order_id)->first();
        if(!$order){
            return response()->json(['status'=>false, 'message'=>'data not found!'], 404);
        }
        return response()->json(['status'=>true, 'response'=>$order, 'message'=>'Data successfully retrieved'],200);
    }

    public function CompanyPolicy(){
        $data = Policy::orderBy('title', 'DESC')->get();
        if(count($data)==0){
            return response()->json(['status'=>false, 'message'=>'Data not found!'], 404);
        }

        return response()->json(['status'=>true, 'response'=>$data, 'message'=>'Data successfully retrieved'],200);
    }
    public function CompanyPolicyDetails($id){
        $data = Policy::where('id', $id)->first();
        if(!$data){
            return response()->json(['status'=>false, 'message'=>'data not found!'], 404);
        }
        return response()->json(['status'=>true, 'response'=>$data, 'message'=>'Data successfully retrieved'],200);
    }

    public function bookNow(Request $request){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id', // Ensure 'id' column exists
            'is_driving_licence_required' => 'required', // If it's a boolean
            // 'subscription_id' => 'required', // Ensure 'id' column exists
            'subscription_type' => 'required|string|max:255',
            'deposit_amount' => 'required|numeric', // Ensure it's a number
            'rental_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);
        
       
        
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                // 'message' => 'Validation failed. Please check the errors below.',
                'message' => $validator->errors()->first(), // Return all errors instead of only the first
            ], 422);
        }
        // Check User Verification
        if ($user->is_verified!=="verified") {
            return response()->json([
                'status' => false, 
                'message' => "Your KYC status is currently $user->is_verified Please verify your KYC to continue."
            ], 404);
        }

        if ($user->vehicle_assign_status == "suspended") {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! Your account is suspended. Please contact the administrator for assistance.',
            ], 404);
        } 
        // Check Driving Licence Verification
        if ($request->is_driving_licence_required==1 && $user->driving_licence_status!=2) {
            return response()->json([
                'status' => false, 
                'message' => "Your driving licence verification is pending. Please complete the verification to continue."
            ], 404);
        }

        // Assuming assigned_vehicle holds the assigned vehicle data
        $assigned_vehicle = AsignedVehicle::where('user_id', $user->id)->where('status', 'assigned')->get()->count();
        if($assigned_vehicle>0){
            return response()->json([
                'status' => false, 
                'message' => "You already have an assigned vehicle. Please use it or return it before booking a new one."
            ], 404);
        }

        $RentalPrice = RentalPrice::where('product_id', $request->product_id)->where('subscription_type', $request->subscription_type)->first();
        if(!$RentalPrice){
            return response()->json([
                'status' => false, 
                 'message' => "This vehicle is not available for the selected duration."
            ], 404);
        }

        $total_amount = $RentalPrice->deposit_amount+$RentalPrice->rental_amount;
        if($request->total_amount!=$total_amount){
            return response()->json([
                'status' => false, 
                  'message' => "The total amount does not match the required amount. Please check and try again."
            ], 404);
        }
// dd($validator);
        DB::beginTransaction();
        try{
            $existing_order = Order::where('user_id', $user->id)->orderBy('id', 'DESC')->first();
          
            if($existing_order){
                if($existing_order->rent_status =="pending"){
                    $existing_order->update([
                        'user_id' => $user->id,
                        'product_id' => (int)$request->product_id,
                        'deposit_amount' =>$RentalPrice->deposit_amount,
                        'rental_amount' => $RentalPrice->rental_amount,
                        'total_price' => $total_amount,
                        'final_amount' => $total_amount,
                        'subscription_id' => (int)$RentalPrice->id,
                        'quantity' => 1,
                        'payment_mode' => "Online",
                        // 'shipping_address' => $request->shipping_address,
                        'rent_duration' => $RentalPrice->duration,
                        'rent_status' => "pending",
                    ]);
                    $order = $existing_order;
                    $message = "Order updated successfully";
                    DB::commit();
                    // $this->bookingNewPayment($order->id);
    
                    return response()->json([
                        'status' => true,
                        'message' => $message,
                        'order' => $order,
                    ], 200);
    
                }elseif ($existing_order->rent_status == "ready to assign") {
                    return response()->json([
                        'status' => false,
                        'message' => 'You already have an order. Please wait for the cab to be assigned by the admin or cancel the order to proceed.',
                    ], 404);
                } elseif ($existing_order->user->vehicle_assign_status == "deallocate") {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have been deallocated. Please return the vehicle and contact the admin.',
                    ], 403);
                } elseif ($existing_order->rent_status == "active") {
                    return response()->json([
                        'status' => false,
                        'message' => 'You already have an active order. Please complete it before creating a new one.',
                    ], 404);
                } elseif ($existing_order->rent_status == "suspended") {
                    return response()->json([
                        'status' => false,
                        'message' => 'Sorry! Your account is suspended. Please contact the administrator for assistance.',
                    ], 403);
                } 
            }
            // else{
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_type' => 'Rent',
                    'order_number' => generateOrderNumber(),
                    'product_id' => (int)$request->product_id,
                    'deposit_amount' =>$RentalPrice->deposit_amount,
                    'rental_amount' => $RentalPrice->rental_amount,
                    'total_price' => $total_amount,
                    'final_amount' => $total_amount,
                    'subscription_id' => (int)$RentalPrice->id,
                    'quantity' => 1,
                    'payment_status' => "pending",
                    // 'shipping_address' => $request->shipping_address,
                    'rent_duration' => $RentalPrice->duration,
                    'rent_status' => "pending",
                ]);

                $message = "Order created successfully";

                DB::commit();

                // $this->bookingNewPayment($order->id);

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'order' => $order,
                ], 200);

           

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bookingNewPayment(Request $request){
       
        DB::beginTransaction();
        try{
            $status = $request->status;
            $order_id = $request->order_id;
            $order_amount = $request->order_amount;
            $razorpay_order_id = $request->razorpay_order_id;
            $razorpay_payment_id = $request->razorpay_payment_id;
            $razorpay_signature = $request->razorpay_signature;
            if($status==true){

                $order = Order::find($order_id);

                $amount = number_format($order_amount, 2, '.', '');
                $orderAmount = number_format($order->final_amount, 2, '.', '');

                if ($orderAmount !== $amount) {
                    return response()->json([
                        'status' => false,
                        'message' => "Sorry, the payment amount (₹$amount) does not match the subscription amount (₹$orderAmount).",
                    ], 403);
                }
                if($order->payment_status=="completed"){
                    return response()->json([
                        'status' => false,
                        'message' => "Payment already completed for this subscription.",
                    ], 403);
                }

                $fetchResponse = $this->PaymentFetch($razorpay_payment_id,$order_id);
                if($fetchResponse['status']){
                    $captureResponse = $this->PaymentCaptured($razorpay_payment_id,$order_amount);

                    if ($captureResponse['status']) {
                        if($captureResponse['data']['status']=="captured"){
                            $order_type = $order->subscription?Str::snake($order->subscription->subscription_type):"";
                            $payment = Payment::find($fetchResponse['payment_id']);
                            $payment->order_id = $order->id;
                            $payment->user_id = $order->user_id;
                            $payment->order_type = 'new_subscription_'.$order_type;
                            $payment->payment_method = $captureResponse['data']['method'];
                            $payment->currency = $captureResponse['data']['currency'];
                            $payment->payment_status = 'completed';
                            $payment->transaction_id = date('dmyhis');
                            $payment->razorpay_order_id = $razorpay_order_id;
                            $payment->razorpay_payment_id = $razorpay_payment_id;
                            $payment->razorpay_signature = $razorpay_signature;
                            $payment->amount = $order->final_amount;
                            $payment->payment_date = date('Y-m-d h:i:s');
                            $payment->save();
                            if($payment){
                                // Deposit Amount
                                $payment_item = new PaymentItem;
                                $payment_item->payment_id = $payment->id;
                                $payment_item->product_id = $order->product_id;
                                $payment_item->payment_for = 'new_subscription_'.$order_type;
                                $payment_item->duration = $order->rent_duration;
                                $payment_item->type = 'deposit';
                                $payment_item->amount = $order->deposit_amount;
                                $payment_item->save();
            
                                // Rental Amount
                                $payment_item = new PaymentItem;
                                $payment_item->payment_id = $payment->id;
                                $payment_item->product_id = $order->product_id;
                                $payment_item->payment_for = 'new_subscription_'.$order_type;
                                $payment_item->duration = $order->rent_duration;
                                $payment_item->type = 'rental';
                                $payment_item->amount = $order->rental_amount;
                                $payment_item->save();
                            }
            
                            $order->payment_mode = "Online";
                            $order->payment_status = "completed";
                            $order->rent_status = "ready to assign";
                            $order->subscription_type = 'new_subscription_'.$order_type;
                            $order->save();
            
                            DB::commit();

                            return response()->json([
                                'status' => true,
                                'message' => "Payment has been successfully created.",
                            ], 200);
                        }
                        return response()->json([
                            'status' => true,
                            'message' => $captureResponse['message'],
                            'data' => $captureResponse['data']
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => $captureResponse['message'],
                            'error' => $captureResponse['error']
                        ], 500);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => "Payment data not found in the response.",
                    ], 500);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => "Payment failed. Please try again.",
                ], 500);
            }    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function PaymentCaptured($razorpay_payment_id,$amount){
        $api_key = env('RAZORPAY_KEY_ID');
        $api_secret = env('RAZORPAY_KEY_SECRET');
        
        // Curl Initialization
        $curl = curl_init();

        // Razorpay API URL for Payment Capture
        $url = "https://api.razorpay.com/v1/payments/$razorpay_payment_id/capture";
        // dd($url);
        // Curl Configuration
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_USERPWD => $api_key . ":" . $api_secret,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                "amount" => $amount * 100, // Amount in paise (INR 1000 = 100000)
                "currency" => "INR"
            ]),
        ]);

        // Execute Curl Request
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Handle Response
        // dd($response);
        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            return [
                'status' => true,
                'data' => $responseData,
                'message' => "Payment captured successfully."
            ];
        } else {
            return [
                'status' => false,
                'message' => "Failed to capture payment.",
                'error' => json_decode($response, true)
            ];
        }
    }
    private function PaymentFetch($razorpay_payment_id, $order_id)
    {
        // dd($razorpay_payment_id, $order_id);s
        $api_key = env('RAZORPAY_KEY_ID');
        $api_secret = env('RAZORPAY_KEY_SECRET');
        
        // Initialize Curl
        $curl = curl_init();
        $url = "https://api.razorpay.com/v1/payments/$razorpay_payment_id";

        // Curl Configuration
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET", // Corrected to GET
            CURLOPT_USERPWD => $api_key . ":" . $api_secret,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);

        // Execute Curl Request
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Handle Response
        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            // Ensure the payment data is available
            if (isset($responseData['id'])) {
                $order = Order::find($order_id);
                $order_type = $order->subscription ? Str::snake($order->subscription->subscription_type) : "";

                // Create Payment Record
                $payment = Payment::firstOrNew(['razorpay_payment_id' => $razorpay_payment_id]);
               
                $payment->order_id = $order->id;
                $payment->user_id = $order->user_id;
                $payment->order_type = 'new_subscription_' . $order_type;
                $payment->payment_method = $responseData['method'] ?? 'N/A';
                $payment->currency = $responseData['currency'] ?? 'INR';
                $payment->payment_status = $responseData['status'] ?? 'failed';
                $payment->transaction_id = date('dmyhis');
                $payment->razorpay_order_id = $responseData['order_id'] ?? '';
                $payment->razorpay_payment_id = $razorpay_payment_id;
                $payment->razorpay_signature = $responseData['notes']['razorpay_signature'] ?? '';
                $payment->amount = ($responseData['amount'] ?? 0) / 100; // Convert to actual amount
                $payment->payment_date = now();
                $payment->save();
                DB::commit();
                return [
                    'status' => true,
                    'payment_id' => $payment->id,
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Payment data not found in the response.',
                    'payment_id' => null,
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => "Failed to fetch payment details. HTTP Code: $httpCode",
                'payment_id' => null,
            ];
        }
    }

    public function bookingRenewPayment(Request $request){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        
        $order_id = $request->order_id;
        $status = $request->status;
        $order_amount = $request->order_amount;
        $razorpay_order_id = $request->razorpay_order_id;
        $razorpay_payment_id = $request->razorpay_payment_id;
        $razorpay_signature = $request->razorpay_signature;

        $order = Order::with('subscription')->find($order_id);
        if(!$order){
            return response()->json([
                'status' => false,
                'message' => "Order not found.",
            ], 404);
        }
        $assignRider = AsignedVehicle::where('order_id', $order->id)->first();
        if($assignRider->status!="overdue"){
            return response()->json([
                'status' => false,
                'message' => "Renewal not allowed. Please try after the subscription end date.",
            ], 403);
        }

        DB::beginTransaction();
        try{
            if($status==true){
                $amount = number_format($order_amount, 2, '.', '');
                $orderAmount = number_format($order->rental_amount, 2, '.', '');

                if ($orderAmount !== $amount) {
                    return response()->json([
                        'status' => false,
                        'message' => "Sorry, the payment amount (₹$amount) does not match the renewal subscription amount (₹$orderAmount).",
                    ], 403);
                }

                if($order->payment_status=="completed"){
                    return response()->json([
                        'status' => false,
                        'message' => "Payment already completed for this subscription.",
                    ], 403);
                }
                $fetchResponse = $this->PaymentFetch($razorpay_payment_id,$order_id);
                if($fetchResponse['status']){
                    $captureResponse = $this->PaymentCaptured($razorpay_payment_id,$order_amount);
                    if ($captureResponse['status']) {
                        if($captureResponse['data']['status']=="captured"){
                            $order_type = $order->subscription?Str::snake($order->subscription->subscription_type):"";
                            $payment = Payment::find($fetchResponse['payment_id']);
                            $payment->order_id = $order->id;
                            $payment->user_id = $order->user_id;
                            $payment->order_type = 'renewal_subscription_'.$order_type;
                            $payment->payment_method = $captureResponse['data']['method'];
                            $payment->currency = $captureResponse['data']['currency'];
                            $payment->payment_status = 'completed';
                            $payment->transaction_id = date('dmyhis');
                            $payment->razorpay_order_id = $razorpay_order_id;
                            $payment->razorpay_payment_id = $razorpay_payment_id;
                            $payment->razorpay_signature = $razorpay_signature;
    
                            $payment->amount = $order->subscription ? $order->subscription->rental_amount : $order->rental_amount;
                            $payment->payment_date = date('Y-m-d h:i:s');
                            $payment->save();
                
                            if($payment){
                                // Rental Amount
                                $payment_item = new PaymentItem;
                                $payment_item->payment_id = $payment->id;
                                $payment_item->product_id = $order->product_id;
                                $payment_item->payment_for = 'renewal_subscription_'.$order_type;
                                $payment_item->type = 'rental';
                                $payment_item->vehicle_id = $assignRider->vehicle_id;
                                $payment_item->amount = $order->subscription ? $order->subscription->rental_amount : $order->rental_amount;
                                $payment_item->duration = $order->subscription ? $order->subscription->duration : $order->rent_duration;
                                $payment_item->save();
                
                                // Update Order
                                $startDate = Carbon::parse($assignRider->end_date);
                                $endDate = $startDate->copy()->addDays($payment_item->duration);
                                
                                $order->payment_mode = "Online";
                                $order->payment_status = "completed";
                                $order->rental_amount = $payment_item->amount;
                                $order->total_price = $order->deposit_amount+$payment_item->amount;
                                $order->final_amount = $order->deposit_amount+$payment_item->amount;
                                $order->rent_duration = $payment_item->duration;
                                $order->rent_start_date = $startDate;
                                $order->rent_end_date = $endDate;
                                $order->subscription_type = 'renewal_subscription_'.$order_type;
                                $order->save();
                
                                
                
                                DB::table('exchange_vehicles')->insert([
                                    'status'       => "renewal",
                                    'user_id'      => $assignRider->user_id,
                                    'order_id'     => $assignRider->order_id,
                                    'vehicle_id'   => $assignRider->vehicle_id,
                                    'start_date'   => $assignRider->start_date,
                                    'end_date'     => $assignRider->end_date,
                                    'created_at'   => now(),
                                    'updated_at'   => now(),
                                ]); 
                
                                $assignRider->start_date = $startDate;
                                $assignRider->end_date = $endDate;
                                $assignRider->status = "assigned";
                                $assignRider->save();
                
                                DB::commit();
                                
                                return response()->json([
                                    'status' => true,
                                    'message' => "Payment completed and subscription renewed successfully.",
                                ], 200);
                            }
                        }
                        return response()->json([
                            'status' => true,
                            'message' => $captureResponse['message'],
                            'data' => $captureResponse['data']
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => $captureResponse['message'],
                            'error' => $captureResponse['error']
                        ], 500);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => "Payment data not found in the response.",
                    ], 500);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => "Payment failed. Please try again.",
                ], 500);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function bookingCancel($order_id){
        DB::beginTransaction();
        try{
            $order = Order::find($order_id);
            if($order->rent_status=="active"){
                $order->cancel_request = "Yes";
                $order->cancel_request_at = now();
                $order->save();
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => "Your cancellation request has been successfully submitted.",
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                  'message' => "Cancellation request cannot be processed at this moment.",
                ], 403);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myActiveSubscription(){
        $user = $this->getAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // Return the response if the user is not authenticated
        }
        $order = Order::with('vehicle','product')->whereIn('rent_status', ['pending', 'active', 'ready to assign', 'suspended', 'deallocated'])->where('user_id', $user->id)->first();
        if($order){
            $data= [
                'id' => $order->id,
                'order_type' =>$order->order_type,
                'order_number'=>$order->order_number,
                'deposit_amount'=>$order->deposit_amount,
                'rental_amount'=>$order->rental_amount,
                'final_amount'=>$order->final_amount,
                'payment_mode'=>$order->payment_mode,
                'payment_status'=>$order->payment_status,
                'rent_duration'=>$order->rent_duration.' Days',
                'rent_status'=>$order->rent_status,
                'cancel_request'=>$order->cancel_request,
                'cancel_request_at'=>$order->cancel_request_at?date('d-m-Y h:i A', strtotime($order->cancel_request_at)):"N/A",
                'model'=>$order->product?$order->product->title:"N/A",
                'vehicle'=>$order->vehicle?$order->vehicle->stock->vehicle_number:"N/A",
                'vehicle_status' =>$order->vehicle?$order->vehicle->status:"N/A",
                'rent_start_date' =>$order->vehicle?date('d-m-Y h:i A', strtotime($order->vehicle->start_date)):"N/A",
                'rent_end_date' =>$order->vehicle?date('d-m-Y h:i A', strtotime($order->vehicle->end_date)):"N/A",
                'assigned_at' =>$order->vehicle?date('d-m-Y h:i A', strtotime($order->vehicle->assigned_at)):"N/A",
            ];
            return response()->json([
                'status' => true, 
                'data' => $data, 
                'message' => "You have a subscription."
            ], 200);
        }else{
            return response()->json([
                'status' => false, 
                'message' => "No active subscription found."
            ], 404);
        }
        

    }

}
