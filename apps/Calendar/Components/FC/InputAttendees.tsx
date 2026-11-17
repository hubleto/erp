import React, { useState } from 'react';
import Input, { type InputProps, InputMetaContext } from '@hubleto/react-ui/components/fc/Input';
import request from '@hubleto/react-ui/core/Request';
import Translator from '@hubleto/react-ui/core/Translator';

const T = new Translator('Hubleto/App/Community/Calendar/Loader', 'Components/InputAttendees');

// The value is a JSON list of user ids; an empty or invalid value means no attendees.
function parseAttendeeIds(value: any): Array<number> {
  let parsed = value;
  if (typeof value === 'string') {
    try {
      parsed = JSON.parse(value);
    } catch (error) {
      parsed = [];
    }
  }
  if (!Array.isArray(parsed)) return [];

  const attendeeIds: Array<number> = [];
  for (const idUser of parsed) attendeeIds.push(Number(idUser));
  return attendeeIds;
}

function getUserName(user: any): string {
  const fullName = ((user.first_name ?? '') + ' ' + (user.last_name ?? '')).trim();
  return fullName !== '' ? fullName : (user.email ?? '');
}

function getAllUsers(input: any): Array<any> {
  const users: Array<any> = [];
  for (const user of Object.values(input.data ?? {})) users.push(user);
  return users;
}

const ValueComponent = () => {
  const input = React.useContext(InputMetaContext);
  const attendeeIds = parseAttendeeIds(input.value);

  const attendeeNames: Array<string> = [];
  for (const user of getAllUsers(input)) {
    if (attendeeIds.includes(Number(user.id))) attendeeNames.push(getUserName(user));
  }

  if (attendeeNames.length === 0) return <span className='text-gray-400'>{T.translate('No attendees')}</span>;
  return <span>{attendeeNames.join(', ')}</span>;
}

// A search box and a small scrollable list of users with checkboxes.
const InputComponent = () => {
  const input = React.useContext(InputMetaContext);
  const [search, setSearch] = useState('');
  const attendeeIds = parseAttendeeIds(input.value);
  const searchedText = search.trim().toLowerCase();

  const listedUsers: Array<any> = [];
  for (const user of getAllUsers(input)) {
    const userText = (getUserName(user) + ' ' + (user.email ?? '')).toLowerCase();
    if (searchedText === '' || userText.includes(searchedText)) listedUsers.push(user);
  }

  function toggleAttendee(idUser: number, isChecked: boolean) {
    const changedIds: Array<number> = [];
    for (const idAttendee of attendeeIds) {
      if (idAttendee !== idUser) changedIds.push(idAttendee);
    }
    if (isChecked) changedIds.push(idUser);
    input.changeValue(JSON.stringify(changedIds));
  }

  return <div className='flex flex-col gap-1 w-full'>
    <input
      type='text'
      value={search}
      placeholder={T.translate('Search users')}
      onChange={(event) => setSearch(event.target.value)}
    />
    <div className='max-h-40 overflow-y-auto border border-gray-200 rounded p-1'>
      {listedUsers.map((user: any) => <label key={user.id} className='flex gap-2 items-center justify-start w-fit cursor-pointer'>
        <input
          type='checkbox'
          className='w-auto'
          checked={attendeeIds.includes(Number(user.id))}
          disabled={input.readonly}
          onChange={(event) => toggleAttendee(Number(user.id), event.target.checked)}
        />
        <span>{getUserName(user)}</span>
        <span className='text-xs text-gray-400'>{user.email}</span>
      </label>)}
    </div>
  </div>;
}

// Attendees of an activity, stored as a JSON list of user ids.
const InputAttendees = (props: InputProps) => {
  return <Input
    inputClassName='attendees'
    onInit={(input: any) => {
      const usersFromCache = globalThis.hubleto.users;
      if (usersFromCache) {
        input.setIsInitialized(true);
        input.setData(usersFromCache);
        return;
      }
      request.post('api/get-users', {}, {}, (users: any) => {
        input.setIsInitialized(true);
        input.setData(users);
        globalThis.hubleto.users = users;
      });
    }}
    renderValueComponent={() => <ValueComponent />}
    renderInputComponent={() => <InputComponent />}
    {...props}
  />;
}

export default InputAttendees;
