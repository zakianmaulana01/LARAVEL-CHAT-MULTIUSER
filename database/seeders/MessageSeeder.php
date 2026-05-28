<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $conversations = Conversation::with('participants')->get();

        $chatMessages = [
            'Halo, apa kabar?',
            'Baik, kamu gimana?',
            'Alhamdulillah sehat!',
            'Lagi ngerjain apa nih?',
            'Lagi coding, bikin project baru',
            'Wah keren! Project apa?',
            'Chat app, mirip WhatsApp tapi lebih simpel',
            'Mantap! Kapan selesai?',
            'Mudah-mudahan minggu ini ya',
            'Semangat bro! 💪',
            'Thanks! Kamu juga semangat ya',
            'Oke siap, nanti kabarin kalau udah jadi ya',
            'Pasti! Gue share linknya nanti',
            'Ditunggu ya!',
            'Eh btw, makan siang bareng yuk?',
            'Boleh! Jam berapa?',
            'Jam 12 aja, mau?',
            'Oke gas! Di mana?',
            'Di kantin aja gimana?',
            'Sip, see you!',
            'Hei, udah liat pengumuman belum?',
            'Belum, ada apa?',
            'Deadline project dimajuin seminggu 😱',
            'Waduh serius? Kita harus meeting ASAP',
            'Iya, nanti sore bisa?',
            'Bisa, jam 4 ya?',
            'Ok fixed! Siapin materinya ya',
            'Siap boss 👍',
            'Jangan lupa bawa laptop',
            'Noted!',
            'Good morning! Ready for today?',
            'Morning! Yeap, lets go!',
            'Meeting jam 10 jangan lupa ya',
            'Sudah di-remind, thanks!',
            'Gimana progress task kemarin?',
            'Udah hampir selesai, tinggal testing',
            'Nice! Kalau perlu bantuan bilang ya',
            'Oke thanks 🙏',
            'Eh ada bug di production',
            'Duh serius? Apa errornya?',
        ];

        foreach ($conversations as $conversation) {
            $participants = $conversation->participants;
            if ($participants->count() < 2) continue;

            // 5-15 pesan per conversation
            $messageCount = rand(5, 15);
            $startTime = now()->subDays(rand(1, 30));

            for ($i = 0; $i < $messageCount; $i++) {
                $sender = $participants->random();
                $startTime = $startTime->addMinutes(rand(1, 120));

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender->id,
                    'body' => $chatMessages[array_rand($chatMessages)],
                    'is_read' => $i < $messageCount - 2 ? true : (bool) rand(0, 1),
                    'created_at' => $startTime,
                    'updated_at' => $startTime,
                ]);
            }
        }
    }
}
