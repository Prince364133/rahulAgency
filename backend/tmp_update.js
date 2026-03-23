const mongoose = require('mongoose');
const MONGO_URI = 'mongodb+srv://snapshiksha_db:364133@cluster0.whihmus.mongodb.net/horsefiredb?appName=Cluster0';

const settingsSchema = new mongoose.Schema({
  _id: String,
  whatsapp_number: String,
  phone_display: String,
  updated_at: { type: Date, default: Date.now }
}, { collection: 'settings', versionKey: false });

const Settings = mongoose.model('Settings', settingsSchema);

async function run() {
  try {
    await mongoose.connect(MONGO_URI);
    console.log('Connected to DB');
    
    const result = await Settings.findOneAndUpdate(
      { _id: 'app_settings' },
      { 
        whatsapp_number: '910000000000',
        phone_display: 'Support Available'
      },
      { upsert: true, new: true }
    );
    
    console.log('Successfully updated settings:', result);
    process.exit(0);
  } catch (err) {
    console.error('Update failed:', err);
    process.exit(1);
  }
}

run();
