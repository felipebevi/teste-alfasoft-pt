<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    // teste basico padrao
    public function test_example(): void
    {
        $response = $this->get('/contacts');

        $response->assertStatus(200);
    }

    // helper para criar um usuário autenticado para executar os testes
    private function authenticatedUser(): User
    {
        $user = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@admin.com',
            'password' => bcrypt('123456'),
        ]);

        return $user;
    }

    // teste deslogado pode visualizar os contatos mas nao pode criar, editar ou deletar
    public function test_guest_can_view_contacts_but_cannot_create_edit_delete(): void
    {
        $contact = Contact::create([
            'name' => 'Felipe Silva', 'contact' => '123456789', 'email' => 'felipe@test.com',
        ]);

        // guest CAN view list and details
        $this->get(route('contacts.index'))->assertStatus(200);
        $this->get(route('contacts.show', $contact))->assertStatus(200);

        // guest CANNOT create, edit or delete
        $this->get(route('contacts.create'))->assertRedirect(route('login'));
        $this->post(route('contacts.store'), [])->assertRedirect(route('login'));
        $this->get(route('contacts.edit', $contact))->assertRedirect(route('login'));
        $this->put(route('contacts.update', $contact), [])->assertRedirect(route('login'));
        $this->delete(route('contacts.destroy', $contact))->assertRedirect(route('login'));
    }

    public function test_create_validates_name_and_min_6_characters(): void
    {
        $user = $this->authenticatedUser();
        
        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name'    => '',
            'contact' => '123456789',
            'email'   => 'test@test.com',
        ]);

        $response->assertSessionHasErrors('name');

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name'    => 'Ana',
            'contact' => '123456789',
            'email'   => 'test@test.com',
        ]);

        $response->assertSessionHasErrors('name');

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name'    => 'Ana Silva',
            'contact' => '123456789',
            'email'   => 'test@test.com',
        ]);

        $response->assertRedirect(route('contacts.index'));
    }

    public function test_create_validates_contact_must_be_9_digits(): void
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '123', 'email' => 'test@test.com',
        ]);
        $response->assertSessionHasErrors('contact');

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '123456777', 'email' => 'test@test.com',
        ]);
        $response->assertRedirect(route('contacts.index'));
    }
    
    public function test_create_validates_email_must_be_valid(): void
    {
        $user = $this->authenticatedUser();

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '123456789', 'email' => 'not-email',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_create_validates_unique_contact_and_email(): void
    {
        $user = $this->authenticatedUser();

        Contact::create([
            'name' => 'Existing', 'contact' => '123456789', 'email' => 'email@exists.com',
        ]);

        // contato duplicado
        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '123456789', 'email' => 'email@new.com',
        ]);
        $response->assertSessionHasErrors('contact');

        // email duplicado
        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '987654321', 'email' => 'email@exists.com',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_update_validates_fields(): void
    {
        $user = $this->authenticatedUser();
        $contact = Contact::create([
            'name' => 'Felipe Silva', 'contact' => '123456789', 'email' => 'felipe@test.com',
        ]);

        $response = $this->actingAs($user)->put(route('contacts.update', $contact), [
            'name' => 'Felipe Updated', 'contact' => '123456789', 'email' => 'felipe@test.com',
        ]);
        $response->assertRedirect(route('contacts.index'));
        $this->assertDatabaseHas('contacts', ['name' => 'Felipe Updated']);
        
        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '9876', 'email' => 'email@exists.com',
        ]);
        $response->assertSessionHasErrors('contact');

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Felipe Silva', 'contact' => '987654321', 'email' => 'email_error',
        ]);
        $response->assertSessionHasErrors('email');

        $response = $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'lipe', 'contact' => '987654321', 'email' => 'email@exists.com',
        ]);
        $response->assertSessionHasErrors('name');
    }

    public function test_delete_is_soft_delete(): void
    {
        $user = $this->authenticatedUser();
        $contact = Contact::create([
            'name' => 'Felipe Silva', 'contact' => '123456789', 'email' => 'felipe@test.com',
        ]);

        $response = $this->actingAs($user)->delete(route('contacts.destroy', $contact));
        $response->assertRedirect(route('contacts.index'));

        // ainda existe no BD mas nao retorna na consulta padrao
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id, 'deleted_at' => null,
        ]);

        // verifica que o registro preencheu o campo deleted_at
        $this->assertNotNull($contact->fresh()->deleted_at);
    }

}