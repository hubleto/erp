import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import { ProgressBar } from 'primereact/progressbar';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

interface TableValuesProps extends TableProps {
}

interface TableValuesState extends TableState {
}

export default class TableValues extends Table<TableValuesProps, TableValuesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Contacts/Models/Value',
  }

  props: TableValuesProps;
  state: TableValuesState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableValues';

  constructor(props: TableValuesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableValuesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getType(value: string) {
    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      return 'email';
    } else if (/^[\+|0-9| ]+$/.test(value)) {
      return 'number';
    } else if (/(https:\/\/www\.|http:\/\/www\.|https:\/\/|http:\/\/)?[a-zA-Z0-9]{2,}(\.[a-zA-Z0-9]{2,})(\.[a-zA-Z0-9]{2,})?/.test(value)) {
      return 'url'
    } else {
      return 'other';
    }
  }

  getIcon(value: string) {
    switch (this.getType(value)) {
      case 'email': return 'envelope'; break;
      case 'number': return 'phone'; break;
      case 'url': return 'link'; break;
      default: return 'grip-lines'; break;
    }
  }

  render(): JSX.Element {
    if (!this.state.data) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    return <>
      {this.renderFormModal()}
      <div className="flex flex-col gap-2">
        {this.state.data?.data ?
          Object.keys(this.state.data?.data).map((key) => {
            const item = this.state.data.data[key];
            const itemType = this.getType(item.value);
            return <div key={key} className="flex gap-2 items-center">
              <button
                className="btn btn-transparent w-3/4"
                onClick={(e) => {
                  if (this.props.parentForm.state.isInlineEditing) {
                    e.stopPropagation();
                  } else {
                    switch (itemType) {
                      case 'url':
                        window.open(item.value);
                        e.stopPropagation();
                      break;
                      case 'email':
                        window.open('mailto://' + item.value);
                        e.stopPropagation();
                      break;
                      default:
                        this.props.parentForm.setState({isInlineEditing: true});
                      break;
                    }
                  }
                }}
              >
                <span className="icon"><i className={"fas fa-" + this.getIcon(item.value)}></i></span>
                <span className="text w-full" style={{maxHeight: "10em"}}>
                  {this.props.parentForm.state.isInlineEditing ? <>
                    <div className="hubleto component input w-full"><div className="input-element w-full">
                      <input
                        className="w-full bg-blue-50"
                        onChange={(e) => {
                          let newValues = this.state.data.data;
                          newValues[key].value = e.currentTarget.value;
                          newValues[key].type = this.getType(e.currentTarget.value);
                          this.props.parentForm.updateRecord({ VALUES: newValues });
                        }}
                        value={item.value}
                      />
                    </div></div>
                  </> : item.value ?? ''}
                </span>
              </button>
              <div className="w-1/4">
                {this.props.parentForm.state.isInlineEditing ?
                  <Lookup
                    uid={'value_' + item.id + '_id_category'}
                    model='HubletoApp/Community/Contacts/Models/Category'
                    value={item.id_category}
                    onChange={(input: any, value: any) => {
                      let newValues = this.state.data.data;
                      newValues[key].id_category = value;
                      this.props.parentForm.updateRecord({ VALUES: newValues });
                    }}
                  ></Lookup>
                : item.CATEGORY?.name ?? ''}
              </div>
              {this.props.parentForm.state.isInlineEditing ? null : <div>
                <button
                  className="btn btn-transparent"
                  onClick={() => { this.props.parentForm.setState({isInlineEditing: true}); }}
                >
                  <span className="icon"><i className="fas fa-pencil"></i></span>
                </button>
              </div>}
              {this.props.parentForm.state.isInlineEditing ?
                <button
                  className="btn btn-danger"
                  onClick={() => {
                    let newValues = this.state.data.data;
                    delete newValues[key];
                    this.props.parentForm.updateRecord({ VALUES: newValues });
                  }}
                >
                  <span className="icon"><i className="fas fa-trash-can"></i></span>
                </button>
              : null}
            </div>;
          })
        : null}
      </div>
    </>;
  }
}