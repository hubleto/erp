<?php

namespace HubletoApp\Community\Invoices\Models\Dto;

class Invoice {
  private function __construct(
    public int $idProfile,
    public int $idIssuedBy,
    public int $idCustomer,
    public null|string $number,
    public null|string $vs,
    public null|string $cs,
    public null|string $ss,
    public null|\DateTimeImmutable $dateIssue,
    public null|\DateTimeImmutable $dateDelivery,
    public null|\DateTimeImmutable $dateDue,
    public null|\DateTimeImmutable $datePayment,
    public null|string $notes
  ) { }
}