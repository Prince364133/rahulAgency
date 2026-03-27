const mongoose = require('mongoose');

const pixelCodeSchema = new mongoose.Schema({
  pixelId: {
    type: String,
    required: true,
    trim: true
  },
  label: {
    type: String,
    required: true,
    trim: true
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('PixelCode', pixelCodeSchema);
