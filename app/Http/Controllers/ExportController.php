<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;

class ExportController extends Controller
{
    public function exportVideo(Request $request)
    {
        // Request validation
        $request->validate([
            'frames' => 'required|array',
            'frames.*' => 'required|string',
            'duration' => 'required|numeric',
            'fps' => 'required|numeric',
            'resolution' => 'required|string',
            'format' => 'required|string|in:mp4,webm,gif'
        ]);

        try {
            $frames = $request->input('frames');
            $duration = $request->input('duration');
            $fps = $request->input('fps');
            $resolution = $request->input('resolution');
            $format = $request->input('format');
            
            // Temporary directory create karein
            $tempDir = 'temp/' . uniqid('video_export_');
            Storage::makeDirectory($tempDir);
            
            // Frames save karein
            $framePaths = [];
            foreach ($frames as $index => $frameData) {
                $framePath = $tempDir . '/frame_' . str_pad($index, 5, '0', STR_PAD_LEFT) . '.png';
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $frameData));
                Storage::put($framePath, $imageData);
                $framePaths[] = storage_path('app/' . $framePath);
            }
            
            // Output file path
            $outputFilename = 'video_export_' . time() . '.' . $format;
            $outputPath = storage_path('app/public/exports/' . $outputFilename);
            
            // Ensure exports directory exists
            if (!file_exists(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0777, true);
            }
            
            // Video create karein using FFMpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
                'timeout'          => 3600, // 1 hour timeout
                'ffmpeg.threads'   => 12,
            ]);
            
            $video = $ffmpeg->open($framePaths[0]);
            
            // Add remaining frames
            $video->addFilters([
                '-framerate', $fps,
                '-i', storage_path('app/' . $tempDir) . '/frame_%05d.png',
            ]);
            
            // Video format settings
            if ($format === 'mp4') {
                $videoFormat = new X264('aac', 'libx264');
                $videoFormat->setKiloBitrate(5000); // 5 Mbps
            } else if ($format === 'webm') {
                $videoFormat = new WebM();
                $videoFormat->setKiloBitrate(5000);
            } else if ($format === 'gif') {
                $videoFormat = new Gif();
            }
            
            // Resolution set karein
            $width = 1280;
            $height = 720;
            
            if ($resolution === 'high') {
                $width = 1920;
                $height = 1080;
            } else if ($resolution === 'low') {
                $width = 854;
                $height = 480;
            }
            
            $video->filters()->resize(new \FFMpeg\Coordinate\Dimension($width, $height));
            
            // Video save karein
            $video->save($videoFormat, $outputPath);
            
            // Temporary files delete karein
            Storage::deleteDirectory($tempDir);
            
            // Download link return karein
            return response()->json([
                'success' => true,
                'download_url' => asset('storage/exports/' . $outputFilename),
                'message' => 'Video successfully exported'
            ]);
            
        } catch (\Exception $e) {
            // Cleanup in case of error
            if (isset($tempDir)) {
                Storage::deleteDirectory($tempDir);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Video export failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function checkExportStatus($jobId)
    {
        // Agar aap queues use kar rahe hain, toh status check karne ke liye
        $status = Redis::get('export_job_' . $jobId);
        
        return response()->json([
            'job_id' => $jobId,
            'status' => $status ?: 'unknown',
            'progress' => Redis::get('export_progress_' . $jobId) ?: 0
        ]);
    }
    
    public function downloadExport($filename)
    {
        $path = storage_path('app/public/exports/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
