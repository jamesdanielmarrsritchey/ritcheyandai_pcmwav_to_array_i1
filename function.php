<?php
function wavFileToIntArrays($filePath) {
    // Check if the file exists
    if (!file_exists($filePath)) {
        throw new Exception("File does not exist.");
    }

    // Read the file content
    $fileContent = file_get_contents($filePath);

    // Extract header information
    $header = unpack('NchunkSize/Nformat/VsubChunk1Id/VsubChunk1Size/vaudioFormat/vnumChannels/VsampleRate/VbyteRate/vblockAlign/vbitsPerSample', substr($fileContent, 4, 36));

    // Check if the audio format is 1 (PCM)
    if ($header['audioFormat'] != 1) {
        throw new Exception("Only PCM format is supported.");
    }

    // Calculate the byte rate and determine the format based on bits per sample
    $bytesPerSample = $header['bitsPerSample'] / 8;
    $format = 'v'; // Assuming little endian for simplicity
    if ($header['bitsPerSample'] == 16) {
        $format = 's'; // 16-bit signed
    } elseif ($header['bitsPerSample'] == 8) {
        $format = 'C'; // 8-bit unsigned
    } elseif ($header['bitsPerSample'] == 32) {
        $format = 'l'; // 32-bit signed
    } else {
        throw new Exception("Unsupported bit depth.");
    }

    // Calculate the start of the data chunk
    $dataStart = strpos($fileContent, 'data') + 8;

    // Extract audio data
    $audioData = substr($fileContent, $dataStart);

    // Initialize arrays to hold the PCM data for each channel
    $channels = array_fill(0, $header['numChannels'], []);

    // Unpack the audio data
    $totalSamples = strlen($audioData) / ($bytesPerSample * $header['numChannels']);
    for ($i = 0; $i < $totalSamples; $i++) {
        // Extract samples for each channel
        for ($channel = 0; $channel < $header['numChannels']; $channel++) {
            // Calculate the offset
            $offset = ($i * $header['numChannels'] + $channel) * $bytesPerSample;
            // Unpack the sample
            $sample = unpack($format, substr($audioData, $offset, $bytesPerSample))[1];
            // Adjust for unsigned 8-bit samples
            if ($header['bitsPerSample'] == 8) {
                $sample -= 128;
            }
            // Store the sample
            $channels[$channel][] = $sample;
        }
    }

    return $channels;
}
?>