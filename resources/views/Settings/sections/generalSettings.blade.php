<form action="{{ route('settings.update') }}" id="generalSettingsForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">System Name</label>
                <input type="text" name="system_name" class="form-control"
                       value="{{ $settings['system_name'] ?? '' }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Current Logo</label><br>
                 <img src="{{ isset($settings['system_logo']) ? asset($settings['system_logo']) : asset('images/logo.jpg') }}"
                     alt="Logo" width="100" height="100" class="border rounded">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Upload New Logo</label>
                <input type="file" name="system_logo" class="form-control">
            </div>
        </div>
        <div class="save-btn">
         <button type="button" class="btn btn-primary" onclick="generalSettingsForm('generalSettingsForm')">Save Settings</button>
        </div>
        
    </form>