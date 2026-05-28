<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        if ($users->count() < 2) {
            return;
        }

        // Buat 20 percakapan direct antar user acak
        $pairs = [];
        $attempts = 0;
        while (count($pairs) < 20 && $attempts < 100) {
            $user1 = $users->random();
            $user2 = $users->except($user1->id)->random();
            $key = min($user1->id, $user2->id) . '-' . max($user1->id, $user2->id);

            if (!in_array($key, $pairs)) {
                $pairs[] = $key;

                $conversation = Conversation::create([
                    'type' => 'direct',
                    'created_by' => $user1->id,
                ]);

                $conversation->participants()->attach([$user1->id, $user2->id]);
            }
            $attempts++;
        }

        // Tambahkan admin ke beberapa conversation
        $admin = User::where('role', 'superadmin')->first();
        if ($admin) {
            for ($i = 0; $i < 3; $i++) {
                $randomUser = $users->random();
                $conversation = Conversation::create([
                    'type' => 'direct',
                    'created_by' => $admin->id,
                ]);
                $conversation->participants()->attach([$admin->id, $randomUser->id]);
            }
        }
    }
}
