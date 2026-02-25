<?php

namespace Hubleto\App\Community\Contacts\McpTools;

use Hubleto\Erp\McpTool;

class ContactsTool extends McpTool {
  /**
   * Get information about contact by providing its email
   * address.
   * @param string $email The email of the contact to look for.
   */
  #[McpTool]
  public function getContactByEmail(string $email): string {
    return ".." . $email . "..";
  }
}
