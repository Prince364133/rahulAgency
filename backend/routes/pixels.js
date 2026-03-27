const express = require('express');
const router = express.Router();
const PixelCode = require('../models/PixelCode');

// GET /api/pixels - Public (for thank you page)
router.get('/', async (req, res) => {
  try {
    const pixels = await PixelCode.find().sort({ createdAt: -1 });
    res.json({ status: 'success', data: pixels });
  } catch (error) {
    console.error('Error fetching pixels:', error);
    res.status(500).json({ status: 'error', message: 'Internal Server Error' });
  }
});

// POST /api/pixels - Public (as requested "no auth needed")
router.post('/', async (req, res) => {
  try {
    const { pixelId, label } = req.body;
    
    if (!pixelId || !label) {
      return res.status(400).json({ status: 'error', message: 'Pixel ID and Label are required' });
    }

    const newPixel = new PixelCode({ pixelId, label });
    await newPixel.save();

    res.status(201).json({ status: 'success', data: newPixel });
  } catch (error) {
    console.error('Error saving pixel:', error);
    res.status(500).json({ status: 'error', message: 'Internal Server Error' });
  }
});

// DELETE /api/pixels/:id - Public
router.delete('/:id', async (req, res) => {
  try {
    const deletedPixel = await PixelCode.findByIdAndDelete(req.params.id);
    if (!deletedPixel) {
      return res.status(404).json({ status: 'error', message: 'Pixel not found' });
    }
    res.json({ status: 'success', message: 'Pixel deleted successfully' });
  } catch (error) {
    console.error('Error deleting pixel:', error);
    res.status(500).json({ status: 'error', message: 'Internal Server Error' });
  }
});

module.exports = router;
