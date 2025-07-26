import React, { Component, createRef, RefObject } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import request from '@hubleto/react-ui/core/Request';

interface FormUserRolesProps extends HubletoFormProps {}

interface FormUserRolesState extends HubletoFormState {
  sortedAllPermissions: any,
  rolePermissions: any,
  dataLoading: boolean,
  dataCalled: boolean,
  selectedGroup: string,
}

export default class FormUserRoles<P, S> extends HubletoForm<FormUserRolesProps,FormUserRolesState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Settings/Models/UserRole',
  };

  props: FormUserRolesProps;
  state: FormUserRolesState;

  formRef: any;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\FormUserRole';

  constructor(props: FormUserRolesProps) {
    super(props);
    this.formRef = React.createRef();
    this.state = {
      ...this.getStateFromProps(props),
      sortedAllPermissions: null,
      rolePermissions: null,
      dataLoading: true,
      dataCalled: false,
      selectedGroup: "",
    };
  }

  getStateFromProps(props: FormUserRolesProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  saveRecord(): void {
    this.setState({ dataLoading: true, isInlineEditing: false } as FormUserRolesState);

    if (this.formRef.current) {
      const formData = new FormData(this.formRef.current);
      const formValues = Object.fromEntries(formData.entries());

      request.get(
        'settings/save-permissions',
        {
          roleId: this.state.record.id,
          permissions: formValues,
          roleTitle: this.state.record.role,
          grantAll: this.state.record.grant_all,
        },
        (data: any) => {
          if (data.status == "success") {
            this.loadRecord();
            this.props.parentTable.loadData();
            this.getPermissions();
          }
        }
      );
    }
  }

  // renderSaveButton(): JSX.Element {
  //   let id = this.state.id ? this.state.id : 0;

  //   return <>
  //     {this.state.description?.permissions?.canUpdate ? <button
  //       onClick={() => this.saveRecord()}
  //       className={
  //         "btn btn-add "
  //         + (id <= 0 && this.state.description?.permissions?.canCreate || id > 0 && this.state.description?.permissions?.canUpdate ? "d-block" : "d-none")
  //       }
  //     >
  //       <span className="icon"><i className="fas fa-save"></i></span>
  //       <span className="text">
  //         {this.state.description?.ui?.saveButtonText ?? this.translate("Save")}
  //       </span>
  //     </button> : null}
  //   </>;
  // }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.role ? this.state.record.role : '[Undefined Name]'}</h2>
    </>;
  }

  getPermissions(): any {
    this.setState({ dataLoading: true } as FormUserRolesState);
    request.get(
      'settings/get-permissions',
      {roleId: this.state.record.id},
      (data: any) => {
        if (data.status == "success") {
          this.setState({
            sortedAllPermissions: data.sortedAllPermissions,
            rolePermissions: data.rolePermissions,
            dataLoading: false,
          } as FormUserRolesState)
        }
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (this.state.dataCalled == false) {
      this.setState({ dataCalled: true } as FormUserRolesState)
      this.getPermissions();
    }

    return <>
      {this.state.dataLoading ? <>Loading</> :
        <>
          <div className='card'>
            <div className='card-body'>
              {this.inputWrapper("role")}
              {this.inputWrapper("description")}
              {this.inputWrapper("grant_all")}
            </div>
          </div>
          {R.grant_all == false ?
            <form ref={this.formRef}>
              <div className='card mt-2'>
                <div className='card-header'>
                  <p className='text-bold'>Permissions</p>
                </div>
                <div className='card-body'>
                  {Object.entries(this.state.sortedAllPermissions).map(([app, groups]) => (
                    <div className='card' >
                      <div className='card-header cursor-pointer'
                        onClick={() => {
                          if (this.state.selectedGroup == app) {
                            this.setState({ selectedGroup: "" } as FormUserRolesState);
                          } else this.setState({ selectedGroup: app } as FormUserRolesState)}
                        }
                      >
                        <p>{app}</p>
                        <span className='icon'><i className='fas fa-chevron-down'></i></span>
                      </div>
                      <div className={`card-body ${this.state.selectedGroup == app ? "block" : "hidden"}`}>
                        {Object.entries(groups).map(([key, group]) => (
                          <div className='card card-body mb-2'>
                            <p className='font-bold'>{key}</p>
                            {Object.entries(group).map(([key, permission]) => (
                              <div className='flex flex-row justify-between my-1'>
                                <label htmlFor={`permission_id_${permission.id}`}>{permission.alias ?? permission.permission}</label>
                                <input
                                  id={`permission_id_${permission.id}`}
                                  type='checkbox'
                                  readOnly={this.state.isInlineEditing}
                                  onClick={() => this.setState({ isInlineEditing: true })}
                                  defaultChecked={this.state.rolePermissions.includes(permission.id) ? true : null}
                                  name={permission.id}
                                  value={permission.id}
                                />
                              </div>
                            ))}
                          </div>
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </form>
          : <></>}
        </>

      }
    </>;
  }
}
