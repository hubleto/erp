import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormUser from './FormUser';

interface TableUsersProps extends TableProps {
}

interface TableUsersState extends TableState {
}

export default class TableUsers extends Table<TableUsersProps, TableUsersState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/User',
  }

  props: TableUsersProps;
  state: TableUsersState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\TableUsers';

  constructor(props: TableUsersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered';
    return params;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "roles") {
      return data.ROLES.map((role, key) => {
        return <div className='badge' key={data.id + '-roles-' + key}>{role.role}</div>;
      });
    } else if (columnName == "teams") {
      return data.TEAMS.map((team, key) => {
        return <div
          className='badge' key={data.id + '-roles-' + key}
          style={{borderLeft: '1em solid ' + team.color}}
        >{team.name}</div>;
      });
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormUser {...formDescription}/>;
  }
}