<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                <br>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                <br>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Upload Form -->
                    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="upload-form">
                        @csrf
                        <!-- Drag-and-Drop Area -->
                        <div class="mt-4 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <p class="text-gray-500">Drag and drop an image here, or click to select a file.</p>
                            <input type="file" name="file" id="file-drop" class="hidden" accept="image/jpeg,image/png,image/gif">
                            <!-- Validation Error Message -->
                            <p id="file-error" class="mt-2 text-sm text-red-600 hidden">Please upload an image file.</p>
                        </div>

                        <!-- Upload Button -->
                        <div>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Drag-and-Drop and Validation -->
    <script>
        const dropArea = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('file-drop');
        const fileError = document.getElementById('file-error');
        const uploadForm = document.getElementById('upload-form');

        // Drag-and-Drop Functionality
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-indigo-500');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-indigo-500');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-indigo-500');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileError.classList.add('hidden'); // Hide error message if file is selected
            }
        });

        dropArea.addEventListener('click', () => {
            fileInput.click();
        });

        // File Input Change Event
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileError.classList.add('hidden'); // Hide error message if file is selected
            }
        });

        // Form Submission Validation
        uploadForm.addEventListener('submit', (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault(); // Prevent form submission
                fileError.classList.remove('hidden'); // Show error message
            }
        });
    </script>
</x-app-layout>
