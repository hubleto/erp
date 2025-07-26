import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormContact, { FormContactProps, FormContactState } from './FormContact';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import request from '@hubleto/react-ui/core/Request';
import { ProgressBar } from 'primereact/progressbar';

interface TableContactsProps extends HubletoTableProps {}

interface TableContactsState extends HubletoTableState {
  tableValuesDescription?: any,
}

export default class TableContacts extends HubletoTable<TableContactsProps, TableContactsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Contacts/Models/Contact',
  }

  props: TableContactsProps;
  state: TableContactsState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableContacts';

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: (this.props.customEndpointParams?.idCustomer ? 'right theme-secondary' : 'right wide'),
    };
  }

  getFormProps(): any {
    var formProps = super.getFormProps();
    return {
      ...super.getFormProps(),
      onSaveCallback: (form: FormContact<FormContactProps, FormContactState>, saveResponse: any) => {
        formProps.onSaveCallback(form, saveResponse);
        if (this.props.parentForm) {
          this.props.parentForm.reload();
        }
      },
      onDeleteCallback: (form: FormContact<FormContactProps, FormContactState>, saveResponse: any) => {
        formProps.onDeleteCallback(form, saveResponse);
        if (this.props.parentForm) {
          this.props.parentForm.reload();
        }
      }
    }
  }

  onAfterLoadTableDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Contacts/Models/Value',
        idContact: this.props.recordId ?? description.idContact,
      },
      (description: any) => {
        this.setState({tableValuesDescription: description} as TableContactsState);
      }
    );
    return description;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "virt_tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div
              key={key}
              style={{backgroundColor: tag.TAG.color}}
              className='badge'
            >{tag.TAG.name}</div>;
          })}
        </>
      );
    } else if (data.VALUES && data.VALUES.length > 0) {
      if (columnName == "virt_email") {
        let contactsRendered = 0;
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.VALUES.map((value, key) => {
              if (value.type == "email" && contactsRendered < 2) {
                contactsRendered += 1;
                return (
                  <div className='border border-gray-400 rounded px-1' key={data.id + '-email-' + key}>
                    {value.value} {value.CATEGORY ? <>({value.CATEGORY.name})</> : null}
                  </div>
                );
              } else return null;
            })}
          </div>
        );
      } else if (columnName == "virt_number") {
        let contactsRendered = 0;
        return (
          <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
            {data.VALUES.map((value, key) => {
              if (value.type == "number" && contactsRendered < 2) {
                contactsRendered += 1;
                return (
                  <div className='border border-gray-400 rounded px-1' key={data.id + '-number-' + key}>
                    {value.value} {value.CATEGORY ? <>({value.CATEGORY.name})</> : null}
                  </div>
                );
              } else return null;
            })}
          </div>
        );
      } else return super.renderCell(columnName, column, data, options);
    } else return super.renderCell(columnName, column, data, options);
  }

  renderForm(): JSX.Element {
    let formProps: FormContactProps = this.getFormProps();
    formProps.tableValuesDescription = this.state.tableValuesDescription;
    return <FormContact {...formProps}/>;
  }

  render(): JSX.Element {
    if (this.props.parentForm) {
      if (!this.state.data) {
        return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
      }

      return <>
        {this.renderHeaderButtons()}
        {this.renderFulltextSearch()}
        {this.renderFormModal()}
        <div className="flex gap-2 flex-wrap mt-2">
          {Object.keys(this.state.data?.data).map((key) => {
            const item = this.state.data.data[key];
            return <div key={key}>
              <button
                className="btn btn-transparent"
                onClick={() => {
                  this.setState({recordId: item.id})
                }}
              >
                <span className="icon flex flex-col gap-2">
                  <i className="fas fa-user text-2xl"></i>
                  {item.is_primary ? <div className="badge badge-violet">{this.translate("Primary")}</div> : null}
                </span>
                <span className="text" style={{maxHeight: "10em"}}>
                  <div className="flex gap-2">
                    {item.salutation ?? ''}
                    <b>{item.first_name ?? ''}</b>
                    <b>{item.last_name ?? ''}</b>
                  </div>
                  <div className="flex gap-2">
                    {item.TAGS.map((tag, index) => {
                      return <div
                        key={index}
                        className="rounded"
                        style={{color: tag.TAG.color, border: '1px solid ' + tag.TAG.color, padding: '0 3px'}}
                      >
                        <small>{tag.TAG.name}</small>
                      </div>
                    })}
                  </div>
                  {item.VALUES.map((value, index) => {
                    return <div key={index}><small>{value.value}</small></div>
                  })}
                </span>
              </button>
            </div>;
          })}
        </div>
      </>;
    } else {
      return super.render();
    }
  }
}