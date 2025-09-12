<?php

namespace Hubleto\App\Community\Auth\Interfaces;

interface AuthInterface {
  public array $user { get; set; }

  public function init(): void;
  public function getUserFromSession(): array;
  public function updateUserInSession(array $user): void;
  public function isUserInSession(): bool;
  public function deleteSession();
  public function signIn(array $user);
  public function signOut();
  public function getActiveUsers(): array;
  public function auth(): void;
  public function forgotPassword(): void;
  public function resetPassword(): void;
  public function getUser(): array;
  public function getUserType(): int;
  public function getUserRoles(): array;
  public function userHasRole(int $idRole): bool;
  public function getUserId(): int;
  public function getUserEmail(): string;
  public function setUserLanguage(string $language): void;
  public function getUserLanguage(): string;

}