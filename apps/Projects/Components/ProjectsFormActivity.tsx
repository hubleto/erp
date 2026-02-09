import React, { Component } from 'react';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'

export interface ProjectsFormActivityProps extends FormActivityProps {
  idProject: number,
  idCustomer?: number,
}

export interface ProjectsFormActivityState extends FormActivityState {
}

export default class ProjectsFormActivity<P, S> extends FormActivity<ProjectsFormActivityProps, ProjectsFormActivityState> {
  static defaultProps: any = {
    ...FormActivity.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/ProjectActivity',
  };

  props: ProjectsFormActivityProps;
  state: ProjectsFormActivityState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

  getActivitySourceReadable(): string
  {
    return this.translate('Project');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_project', {readonly: R.id > 0})}
      <FormInput title={this.translate("Contact")}>
        <Lookup {...this.getInputProps('id_contact')}
          model='Hubleto/App/Community/Contacts/Models/Contact'
          endpoint={`contacts/api/get-customer-contacts`}
          customEndpointParams={{id_customer: this.props.idCustomer}}
          value={R.id_contact}
          onChange={(input: any, value: any) => {
            this.updateRecord({ id_contact: value })
            if (R.id_contact == 0) {
              R.id_contact = null;
              this.setState({record: R})
            }
          }}
        ></Lookup>
      </FormInput>
    </>;
  }
}
