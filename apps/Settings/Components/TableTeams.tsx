import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormTeam from './FormTeam';

interface TableTeamsProps extends TableProps {
}

interface TableTeamsState extends TableState {
}

export default class TableTeams extends Table<TableTeamsProps, TableTeamsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/Team',
  }

  props: TableTeamsProps;
  state: TableTeamsState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\TableTeams';

  constructor(props: TableTeamsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered';
    return params;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "members") {
      return data.MEMBERS.map((member, key) => {
        return <div className='badge' key={data.id + '-members-' + key}>{member.MEMBER.email}</div>;
      });
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormTeam {...formDescription}/>;
  }
}