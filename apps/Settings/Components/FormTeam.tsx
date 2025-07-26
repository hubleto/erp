import React, { Component } from 'react'
import { deepObjectMerge } from "@hubleto/react-ui/core/Helper";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormTeamProps extends HubletoFormProps { }
interface FormTeamState extends HubletoFormState { }

export default class FormTeam<P, S> extends HubletoForm<FormTeamProps, FormTeamState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Settings/Models/Team',
  }

  props: FormTeamProps;
  state: FormTeamState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\FormTeam';

  constructor(props: FormTeamProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.name ?? '-'}</h2>
      <small>Team</small>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='w-full flex gap-2'>
        <div className="p-4 flex-1 text-center">
          <i className="fas fa-users text-primary" style={{fontSize: '8em'}}></i>
        </div>
        <div className="flex-6">
          {this.inputWrapper('name')}
          {this.inputWrapper('color')}
          {this.inputWrapper('description')}
          {this.inputWrapper('id_manager')}
          {this.divider('Team members')}
          {this.state.id < 0 ?
            <div className="badge badge-info">First create team, then you will be prompted to add members.</div>
          :
            <Table
              uid='teams_members'
              model='HubletoApp/Community/Settings/Models/TeamMember'
              customEndpointParams={{idTeam: this.state.id}}
            ></Table>
          }
        </div>
      </div>
    </>;
  }
}
