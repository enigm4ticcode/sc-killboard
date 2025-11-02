<?php

use App\Livewire\Organization as OrgComponent;
use App\Livewire\Player as PlayerComponent;
use App\Models\Kill;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Weapon;
use Carbon\Carbon;

use function Pest\Laravel\get;

it('renders the player page when the player exists', function (): void {
    $org = Organization::query()->create([
        'name' => 'Pilots Org',
        'spectrum_id' => 'PILOT',
    ]);

    $player = Player::query()->create([
        'name' => 'Test Pilot',
    ]);
    $player->organization()->associate($org);
    $player->save();

    $weapon = Weapon::query()->create([
        'slug' => 'p4-ar',
        'name' => 'P4-AR',
    ]);

    // Create one kill and one loss for the player
    $opponentOrg = Organization::query()->create([
        'name' => 'Opp Org',
        'spectrum_id' => 'OPP',
    ]);

    $opponent = Player::query()->create(['name' => 'Opponent']);
    $opponent->organization()->associate($opponentOrg);
    $opponent->save();

    Kill::query()->create([
        'destroyed_at' => Carbon::now()->subDay(),
        'weapon_id' => $weapon->id,
        'victim_id' => $opponent->id,
        'killer_id' => $player->id,
        'type' => 'fps',
        'location' => 'Area18',
    ]);

    Kill::query()->create([
        'destroyed_at' => Carbon::now()->subDay(),
        'weapon_id' => $weapon->id,
        'victim_id' => $player->id,
        'killer_id' => $opponent->id,
        'type' => 'fps',
        'location' => 'Orison',
    ]);

    get(route('player.show', ['name' => $player->name]))
        ->assertOk()
        ->assertSeeLivewire(PlayerComponent::class);
});

it('renders the organization page when the organization exists', function (): void {
    $org = Organization::query()->create([
        'name' => 'Test Org',
        'spectrum_id' => 'TEST',
    ]);

    $weapon = Weapon::query()->create([
        'slug' => 'p8-sc',
        'name' => 'P8-SC',
    ]);

    $member = Player::query()->create([
        'name' => 'Member One',
        'organization_id' => $org->id,
    ]);

    $opponentOrg = Organization::query()->create([
        'name' => 'Opponent Org',
        'spectrum_id' => 'OPP2',
    ]);

    $opponent = Player::query()->create(['name' => 'Opponent Two']);
    $opponent->organization()->associate($opponentOrg);
    $opponent->save();

    Kill::query()->create([
        'destroyed_at' => Carbon::now()->subDay(),
        'weapon_id' => $weapon->id,
        'victim_id' => $opponent->id,
        'killer_id' => $member->id,
        'type' => 'fps',
        'location' => 'Yela',
    ]);

    get(route('organization.show', ['name' => $org->spectrum_id]))
        ->assertOk()
        ->assertSeeLivewire(OrgComponent::class);
});
