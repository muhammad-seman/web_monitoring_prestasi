<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class CommunicationController extends Controller
{
    /**
     * Display parent-teacher communication hub
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $anak = $user->anak()->with(['kelas', 'prestasi'])->get();
        
        $communicationData = [
            'recent_communications' => $this->getRecentCommunications($user),
            'active_conversations' => $this->getActiveConversations($user),
            'scheduled_meetings' => $this->getScheduledMeetings($user),
            'communication_statistics' => $this->getCommunicationStatistics($user),
            'teacher_contacts' => $this->getTeacherContacts($anak),
            'quick_actions' => $this->getQuickActions($anak),
            'notification_preferences' => $this->getNotificationPreferences($user)
        ];
        
        return view('wali.communication.index', compact('communicationData'));
    }
    
    /**
     * Send message to teacher
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:siswa,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|in:academic,behavioral,achievement,administrative'
        ]);
        
        $user = Auth::user();
        
        // Verify that the student belongs to this parent
        if (!$user->anak->contains($request->student_id)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        // In real implementation, this would save to a messages table
        $messageData = [
            'id' => rand(1000, 9999),
            'from_user_id' => $user->id,
            'to_user_id' => $request->teacher_id,
            'student_id' => $request->student_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'sent',
            'sent_at' => now(),
            'read_at' => null
        ];
        
        // Send notification to teacher (email, WhatsApp, etc.)
        $this->notifyTeacher($messageData);
        
        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim ke guru',
            'data' => $messageData
        ]);
    }
    
    /**
     * Schedule meeting with teacher
     */
    public function scheduleMeeting(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:siswa,id',
            'meeting_date' => 'required|date|after:now',
            'meeting_time' => 'required|string',
            'duration' => 'required|integer|min:15|max:120',
            'meeting_type' => 'required|in:in_person,video_call,phone_call',
            'agenda' => 'required|string|max:1000',
            'priority' => 'required|in:normal,urgent'
        ]);
        
        $user = Auth::user();
        
        // Verify that the student belongs to this parent
        if (!$user->anak->contains($request->student_id)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        // In real implementation, this would save to a meetings table
        $meetingData = [
            'id' => rand(1000, 9999),
            'parent_id' => $user->id,
            'teacher_id' => $request->teacher_id,
            'student_id' => $request->student_id,
            'meeting_date' => $request->meeting_date,
            'meeting_time' => $request->meeting_time,
            'duration' => $request->duration,
            'meeting_type' => $request->meeting_type,
            'agenda' => $request->agenda,
            'priority' => $request->priority,
            'status' => 'scheduled',
            'created_at' => now()
        ];
        
        // Send calendar invite and notifications
        $this->sendMeetingInvite($meetingData);
        
        return response()->json([
            'success' => true,
            'message' => 'Pertemuan berhasil dijadwalkan',
            'data' => $meetingData
        ]);
    }
    
    /**
     * Get conversation history
     */
    public function getConversation($teacherId, Request $request)
    {
        $user = Auth::user();
        $studentId = $request->get('student_id');
        
        // In real implementation, this would query messages table
        $messages = $this->generateConversationHistory($user->id, $teacherId, $studentId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
                'total_messages' => count($messages),
                'unread_count' => collect($messages)->where('is_read', false)->count(),
                'last_activity' => collect($messages)->max('timestamp')
            ]
        ]);
    }
    
    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        $request->validate([
            'achievement_notifications' => 'boolean',
            'meeting_reminders' => 'boolean',
            'grade_updates' => 'boolean',
            'behavior_alerts' => 'boolean',
            'general_announcements' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'notification_frequency' => 'required|in:immediate,daily,weekly'
        ]);
        
        $user = Auth::user();
        
        // In real implementation, save to user_notification_preferences table
        $preferences = $request->only([
            'achievement_notifications',
            'meeting_reminders', 
            'grade_updates',
            'behavior_alerts',
            'general_announcements',
            'email_notifications',
            'sms_notifications',
            'whatsapp_notifications',
            'notification_frequency'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Preferensi notifikasi berhasil diupdate',
            'data' => $preferences
        ]);
    }
    
    /**
     * Get communication analytics
     */
    public function analytics(Request $request)
    {
        $user = Auth::user();
        
        $analytics = [
            'communication_summary' => $this->getCommunicationSummary($user),
            'response_metrics' => $this->getResponseMetrics($user),
            'teacher_interaction_stats' => $this->getTeacherInteractionStats($user),
            'topic_distribution' => $this->getTopicDistribution($user),
            'meeting_analytics' => $this->getMeetingAnalytics($user),
            'satisfaction_metrics' => $this->getSatisfactionMetrics($user),
            'trend_analysis' => $this->getTrendAnalysis($user)
        ];
        
        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }
    
    /**
     * Export communication report
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'pdf'); // pdf, excel
        $dateRange = $request->get('date_range', 'last_month');
        
        $reportData = [
            'parent_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'children' => $user->anak->pluck('nama')->toArray()
            ],
            'communication_summary' => $this->getCommunicationReportSummary($user, $dateRange),
            'detailed_communications' => $this->getDetailedCommunications($user, $dateRange),
            'meeting_history' => $this->getMeetingHistory($user, $dateRange),
            'response_analytics' => $this->getResponseAnalytics($user, $dateRange),
            'generated_at' => now()
        ];
        
        // In real implementation, generate PDF/Excel file
        $filename = "communication_report_{$user->id}_" . now()->format('Y-m-d') . ".{$format}";
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan komunikasi berhasil dibuat',
            'download_url' => "/downloads/reports/{$filename}",
            'data' => $reportData
        ]);
    }
    
    // Helper methods for communication data
    private function getRecentCommunications($user)
    {
        // Simulated recent communications
        $communications = [];
        
        for ($i = 1; $i <= 15; $i++) {
            $date = Carbon::now()->subDays(rand(1, 30));
            $communications[] = [
                'id' => $i,
                'type' => ['message', 'meeting', 'phone_call', 'email'][rand(0, 3)],
                'teacher_name' => 'Bapak/Ibu ' . ['Ahmad', 'Siti', 'Budi', 'Dewi'][rand(0, 3)],
                'student_name' => $user->anak->first()->nama ?? 'Anak',
                'subject' => $this->generateCommunicationSubject(),
                'preview' => $this->generateCommunicationPreview(),
                'date' => $date,
                'status' => ['read', 'unread', 'replied'][rand(0, 2)],
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'category' => ['academic', 'behavioral', 'achievement', 'administrative'][rand(0, 3)]
            ];
        }
        
        return collect($communications)->sortByDesc('date')->values()->all();
    }
    
    private function getActiveConversations($user)
    {
        return [
            [
                'teacher_id' => 1,
                'teacher_name' => 'Bapak Ahmad',
                'subject' => 'Diskusi Prestasi Matematika',
                'last_message' => 'Terima kasih informasinya, Bu. Saya akan follow up.',
                'last_activity' => Carbon::now()->subHours(2),
                'unread_count' => 1,
                'student_name' => $user->anak->first()->nama ?? 'Anak'
            ],
            [
                'teacher_id' => 2,
                'teacher_name' => 'Ibu Siti',
                'subject' => 'Rencana Kegiatan Ekstrakurikuler',
                'last_message' => 'Jadwal latihan sudah diupdate.',
                'last_activity' => Carbon::now()->subHours(5),
                'unread_count' => 0,
                'student_name' => $user->anak->first()->nama ?? 'Anak'
            ]
        ];
    }
    
    private function getScheduledMeetings($user)
    {
        return [
            [
                'id' => 1,
                'teacher_name' => 'Bapak Ahmad',
                'student_name' => $user->anak->first()->nama ?? 'Anak',
                'date' => Carbon::now()->addDays(3),
                'time' => '10:00',
                'duration' => 30,
                'type' => 'video_call',
                'agenda' => 'Evaluasi prestasi semester dan rencana ke depan',
                'status' => 'confirmed'
            ],
            [
                'id' => 2,
                'teacher_name' => 'Ibu Dewi',
                'student_name' => $user->anak->first()->nama ?? 'Anak',
                'date' => Carbon::now()->addDays(7),
                'time' => '14:00',
                'duration' => 45,
                'type' => 'in_person',
                'agenda' => 'Konsultasi pemilihan ekstrakurikuler',
                'status' => 'pending'
            ]
        ];
    }
    
    private function getCommunicationStatistics($user)
    {
        return [
            'total_messages_sent' => rand(50, 100),
            'total_messages_received' => rand(60, 120),
            'average_response_time' => '4.2 jam',
            'meetings_scheduled' => rand(8, 15),
            'meetings_completed' => rand(5, 12),
            'active_conversations' => rand(2, 5),
            'communication_frequency' => '2.3 per minggu',
            'satisfaction_rating' => rand(4.0, 4.9)
        ];
    }
    
    private function getTeacherContacts($anak)
    {
        $contacts = [];
        
        foreach ($anak as $child) {
            if ($child->kelas) {
                $contacts[] = [
                    'teacher_id' => rand(1, 10),
                    'teacher_name' => 'Wali Kelas ' . $child->kelas->nama_kelas,
                    'subject' => 'Wali Kelas',
                    'student_name' => $child->nama,
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'email' => 'guru' . rand(1, 10) . '@school.edu',
                    'availability' => 'Senin-Jumat 08:00-15:00',
                    'preferred_contact' => ['WhatsApp', 'Email', 'Phone'][rand(0, 2)]
                ];
            }
        }
        
        return $contacts;
    }
    
    private function getQuickActions($anak)
    {
        return [
            [
                'label' => 'Tanya Prestasi Terbaru',
                'icon' => 'trophy',
                'template' => 'Mohon informasi terkini mengenai prestasi anak saya dalam bidang...'
            ],
            [
                'label' => 'Jadwalkan Konsultasi',
                'icon' => 'calendar',
                'template' => 'Saya ingin menjadwalkan pertemuan untuk membahas...'
            ],
            [
                'label' => 'Feedback Pembelajaran',
                'icon' => 'comment',
                'template' => 'Bagaimana perkembangan anak saya dalam mata pelajaran...'
            ],
            [
                'label' => 'Info Kegiatan',
                'icon' => 'info-circle',
                'template' => 'Mohon informasi mengenai kegiatan ekstrakurikuler yang...'
            ]
        ];
    }
    
    private function getNotificationPreferences($user)
    {
        return [
            'achievement_notifications' => true,
            'meeting_reminders' => true,
            'grade_updates' => true,
            'behavior_alerts' => true,
            'general_announcements' => false,
            'email_notifications' => true,
            'sms_notifications' => false,
            'whatsapp_notifications' => true,
            'notification_frequency' => 'immediate'
        ];
    }
    
    private function generateConversationHistory($parentId, $teacherId, $studentId)
    {
        $messages = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $isFromParent = rand(0, 1) == 1;
            $date = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 23));
            
            $messages[] = [
                'id' => $i,
                'from_user_id' => $isFromParent ? $parentId : $teacherId,
                'to_user_id' => $isFromParent ? $teacherId : $parentId,
                'message' => $this->generateMessageContent($isFromParent),
                'timestamp' => $date,
                'is_read' => rand(0, 1) == 1,
                'sender_type' => $isFromParent ? 'parent' : 'teacher',
                'sender_name' => $isFromParent ? 'Orang Tua' : 'Guru'
            ];
        }
        
        return collect($messages)->sortBy('timestamp')->values()->all();
    }
    
    private function generateMessageContent($isFromParent)
    {
        if ($isFromParent) {
            $messages = [
                'Bagaimana perkembangan anak saya akhir-akhir ini?',
                'Terima kasih atas bimbingannya selama ini.',
                'Apakah ada kegiatan ekstrakurikuler yang bisa diikuti?',
                'Mohon informasi terkait prestasi anak saya bulan ini.',
                'Saya ingin berdiskusi mengenai rencana pembelajaran ke depan.'
            ];
        } else {
            $messages = [
                'Alhamdulillah, anak Bapak/Ibu menunjukkan perkembangan yang baik.',
                'Saya akan terus memantau dan memberikan bimbingan terbaik.',
                'Ada beberapa kompetisi yang cocok untuk anak Bapak/Ibu.',
                'Prestasi anak Bapak/Ibu bulan ini sangat membanggakan.',
                'Mari kita diskusikan lebih lanjut mengenai potensi anak.'
            ];
        }
        
        return $messages[array_rand($messages)];
    }
    
    private function generateCommunicationSubject()
    {
        $subjects = [
            'Diskusi Prestasi Anak',
            'Konsultasi Perkembangan',
            'Info Kegiatan Sekolah',
            'Feedback Pembelajaran',
            'Rencana Ekstrakurikuler',
            'Evaluasi Semester',
            'Persiapan Kompetisi'
        ];
        return $subjects[array_rand($subjects)];
    }
    
    private function generateCommunicationPreview()
    {
        $previews = [
            'Perkembangan anak sangat baik, mari diskusi lebih lanjut...',
            'Ada beberapa kompetisi menarik yang bisa diikuti...',
            'Prestasi bulan ini menunjukkan peningkatan signifikan...',
            'Saya ingin membahas rencana pembelajaran selanjutnya...',
            'Terima kasih atas dukungan dan kerjasamanya...'
        ];
        return $previews[array_rand($previews)];
    }
    
    private function notifyTeacher($messageData)
    {
        // Send email, WhatsApp, or push notification to teacher
        // Implementation depends on notification service
    }
    
    private function sendMeetingInvite($meetingData)
    {
        // Send calendar invite and notifications
        // Implementation depends on calendar service
    }
    
    // Placeholder implementations for analytics methods
    private function getCommunicationSummary($user) { return []; }
    private function getResponseMetrics($user) { return []; }
    private function getTeacherInteractionStats($user) { return []; }
    private function getTopicDistribution($user) { return []; }
    private function getMeetingAnalytics($user) { return []; }
    private function getSatisfactionMetrics($user) { return []; }
    private function getTrendAnalysis($user) { return []; }
    private function getCommunicationReportSummary($user, $dateRange) { return []; }
    private function getDetailedCommunications($user, $dateRange) { return []; }
    private function getMeetingHistory($user, $dateRange) { return []; }
    private function getResponseAnalytics($user, $dateRange) { return []; }
}