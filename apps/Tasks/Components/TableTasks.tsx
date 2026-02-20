import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import FormTask from './FormTask';
import FormActivity from '@hubleto/apps/Worksheets/Components/FormActivity';

interface TableTasksProps extends TableExtendedProps {
  idCustomer?: any,
}

interface TableTasksState extends TableExtendedState {
  addActivityForIdTask: number,
}

export default class TableTasks extends TableExtended<TableTasksProps, TableTasksState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Tasks/Models/Task',
  }

  props: TableTasksProps;
  state: TableTasksState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks\\Loader';
  translationContextInner: string = 'Components\\TableTasks';

  refActivityModal: any;
  refActivityForm: any;

  constructor(props: TableTasksProps) {
    super(props);
    this.refActivityModal = React.createRef();
    this.refActivityForm = React.createRef();
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTasksProps) {
    return {
      ...super.getStateFromProps(props),
      addActivityForIdTask: 0,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCustomer: this.props.idCustomer,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/tasks/' + (id > 0 ? id : 'add'));
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderActionsColumn(data: any, options: any) {
    return <>
      <button
        className="btn btn-small btn-add-outline text-nowrap"
        onClick={(e) => {
          e.preventDefault();
          this.setState({addActivityForIdTask: data.id});
        }}
      >
        <span className="icon"><i className="fas fa-plus"></i></span>
        <span className="text">{this.translate('Add activity')}</span>
      </button>
    </>;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "title") {
      return <>
        {super.renderCell(columnName, column, data, options)}
        {data['TODO'] ? data['TODO'].map((item, key) => {
          if (item.is_closed) return null;
          else return <div className='text-yellow-600 font-normal text-xs' key={key}>{item.todo}</div>
        }) : null}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.idCustomer = this.props.idCustomer;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_customer: this.props.idCustomer };
    return <FormTask {...formProps}/>;
  }

  renderContent(): JSX.Element {
    return <>
      {super.renderContent()}
      {this.state.addActivityForIdTask > 0 ?
        <ModalForm
          ref={this.refActivityModal}
          uid={this.props.uid + "_add_activity_modal"}
          isOpen={true}
          type='centered small theme-secondary'
          onClose={() => this.setState({addActivityForIdTask: 0})}
          form={this.refActivityForm}
        >
          <FormActivity
            id={-1}
            modal={this.refActivityModal}
            description={{defaultValues: {id_task: this.state.addActivityForIdTask}}}
            onClose={() => {
              this.setState({addActivityForIdTask: 0});
              this.reload();
            }}
          ></FormActivity>
        </ModalForm>
      : null}
    </>;
  }
}