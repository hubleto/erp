import React, { Component, createRef, RefObject } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TablePanels from './TablePanels';

interface FormDashboardProps extends HubletoFormProps {}
interface FormDashboardState extends HubletoFormState {}

export default class FormDashboard<P, S> extends HubletoForm<FormDashboardProps,FormDashboardState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Dashboards/Models/Dashboard',
  };

  props: FormDashboardProps;
  state: FormDashboardState;

  translationContext: string = 'Hubleto\\App\\Community\\Dashboards\\Loader';
  translationContextInner: string = 'Components\\FormDashboard';

  constructor(props: FormDashboardProps) {
    super(props);
  }

  slugify(text: string) {
    return text
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9-]/g, '-')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  }

  getStateFromProps(props: FormDashboardProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderHeaderRight(): null | JSX.Element {
    if (this.state.recordChanged) {
      return <>
        <div
          className='btn btn-transparent px-2'
        >
          {this.translate("Preview available after saving")}
        </div>
        {super.renderHeaderRight()}
      </>
    } else {
      return <>
        <a
          className='btn btn-add px-2'
          target='_blank'
          href={globalThis.hubleto.config.projectUrl+"/dashboards/manage/"+this.state.record.slug}
        >
          {this.translate("Preview")}
        </a>
        {super.renderHeaderRight()}
      </>
    }

  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ?? ''}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      <div className='card'>
        <div className='card-body'>
          {this.inputWrapper("id_owner")}
          {this.inputWrapper("title", {onChange: () => {this.updateRecord({slug: this.slugify(this.state.record.title)})}})}
          {this.inputWrapper("slug")}
          {this.inputWrapper("color")}
          {this.inputWrapper("is_default")}
        </div>
      </div>
      {this.divider(this.translate('Panels'))}
      {this.state.id < 0 ?
        <div className="badge badge-info">First create the dashboard, then you will be prompted to add panels.</div>
      :
        <div className='mt-2'>
          <TablePanels
            uid='dashboard_panels'
            customEndpointParams={{idDashboard: this.state.id}}
          ></TablePanels>
        </div>
      }
    </>
  }
}
